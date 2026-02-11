<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/NotificationService.php';

$notificationService = new NotificationService();
$notifications = [];
$erreur = '';

try {
    $utilisateur = getUtilisateurConnecte();
    $notifications = $notificationService->listerNotificationsUtilisateur($utilisateur->getId());
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>🔔 Notifications</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 1rem;">
            <button onclick="marquerToutesCommeLues()" class="btn">📧 Marquer toutes comme lues</button>
        </div>
        
        <?php if (empty($notifications)): ?>
            <p>Aucune notification.</p>
        <?php else: ?>
            <div id="notifications_container">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?= $notification->getStatutEnvoi() === 'PENDING' ? 'non-lue' : 'lue' ?>" 
                         data-id="<?= $notification->getId() ?>">
                        <div class="notification-header">
                            <span class="notification-type">
                                <?= $notification->getTypeEvenement() === 'CREATION_TACHE' ? '🆕' : 
                                   ($notification->getTypeEvenement() === 'STATUT_TACHE' ? '📊' : '💬') ?>
                            </span>
                            <strong><?= htmlspecialchars($notification->getSujet()) ?></strong>
                            <span class="notification-date">
                                <?= date('d/m/Y H:i', strtotime($notification->getDateCreation())) ?>
                            </span>
                        </div>
                        <div class="notification-contenu">
                            <?= nl2br(htmlspecialchars($notification->getContenu())) ?>
                        </div>
                        <div class="notification-actions">
                            <?php if ($notification->getStatutEnvoi() === 'PENDING'): ?>
                                <button onclick="marquerCommeLue(<?= $notification->getId() ?>)" 
                                        class="btn btn-small">Marquer comme lue</button>
                            <?php endif; ?>
                            
                            <button onclick="supprimerNotification(<?= $notification->getId() ?>)" 
                                    class="btn btn-danger btn-small">Supprimer</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.notification-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #3498db;
}

.notification-item.non-lue {
    background-color: #f8f9fa;
    border-left-color: #e74c3c;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.notification-type {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

.notification-date {
    color: #666;
    font-size: 0.875rem;
}

.notification-contenu {
    margin: 0.5rem 0;
    line-height: 1.5;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
}
</style>

<script>
function marquerCommeLue(notificationId) {
    if (confirm('Marquer cette notification comme lue ?')) {
        fetch('../api/marquer_notification_lue.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notificationId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notification = document.querySelector(`[data-id="${notificationId}"]`);
                notification.classList.remove('non-lue');
                notification.classList.add('lue');
                
                // Mettre à jour le bouton
                const button = notification.querySelector('button');
                if (button && button.textContent.includes('Marquer comme lue')) {
                    button.remove();
                }
                
                afficherMessage('Notification marquée comme lue', 'success');
            } else {
                afficherMessage('Erreur : ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessage('Erreur lors du marquage', 'error');
        });
    }
}

function marquerToutesCommeLues() {
    if (confirm('Marquer toutes les notifications comme lues ?')) {
        const notificationsNonLues = document.querySelectorAll('.notification-item.non-lue');
        
        notificationsNonLues.forEach(notification => {
            const notificationId = notification.dataset.id;
            
            fetch('../api/marquer_notification_lue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `notification_id=${notificationId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notification.classList.remove('non-lue');
                    notification.classList.add('lue');
                    
                    // Supprimer le bouton "Marquer comme lue"
                    const button = notification.querySelector('button');
                    if (button && button.textContent.includes('Marquer comme lue')) {
                        button.remove();
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
        
        const count = notificationsNonLues.length;
        if (count > 0) {
            afficherMessage(`${count} notification(s) marquée(s) comme lue(s)`, 'success');
        }
    }
}

function supprimerNotification(notificationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        fetch('../api/supprimer_notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notificationId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notification = document.querySelector(`[data-id="${notificationId}"]`);
                notification.remove();
                afficherMessage('Notification supprimée', 'success');
            } else {
                afficherMessage('Erreur : ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessage('Erreur lors de la suppression', 'error');
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
