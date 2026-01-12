</div>
    
    <script>
        // Fonctions JavaScript utilitaires
        function confirmer(message) {
            return confirm(message);
        }
        
        function afficherMessage(message, type = 'success') {
            const div = document.createElement('div');
            div.className = type;
            div.textContent = message;
            div.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem;
                border-radius: 4px;
                z-index: 1000;
                max-width: 300px;
            `;
            
            if (type === 'success') {
                div.style.backgroundColor = '#d4edda';
                div.style.color = '#155724';
                div.style.border = '1px solid #c3e6cb';
            } else if (type === 'error') {
                div.style.backgroundColor = '#f8d7da';
                div.style.color = '#721c24';
                div.style.border = '1px solid #f5c6cb';
            }
            
            document.body.appendChild(div);
            
            setTimeout(() => {
                document.body.removeChild(div);
            }, 5000);
        }
    </script>
</body>
</html>
