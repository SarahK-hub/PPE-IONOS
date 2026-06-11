 
<div class="login-box">
 <div class="login-wrapper">
    <img src="/logo-gsb.png" alt="GSB">
 </div>
    <h1>Connexion GSB</h1>

    <?php if (!empty($message)): ?>
        <div class="flash">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
     
                   

                    
    <form method="post" action="/index.php/login">

        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <input type="text" name="username" placeholder="Utilisateur" required>

        <input type="password" name="password" placeholder="Mot de passe" required>

        <button type="submit">Se connecter</button>

    </form>

</div>
  </div>