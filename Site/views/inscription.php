<div class="containerBody">
    <h1>Inscription</h1>

    <form class="formulaire" method="post">

        <div class="input-group">
            <label for="email" class="group-label">E-mail</label>
            <input type="email" id="email" class="input" placeholder="Mettez votre e-mail" required>
        </div>

        <div class="input-group">
            <label for="password" class="group-label">Mot de passe</label>
            <input type="password" id="password" class="input" placeholder="Mettez votre mot de passe" required>
        </div>


        <div class="input-group">
            <label for="password" class="group-label">Confirmation Mot de passe</label>
            <input type="password" id="password" class="input" placeholder="Confirmez votre mot de passe" required>
        </div>


        <div class="input-group">
            <label for="pseudo" class="group-label">Pseudo utilisateur</label>
            <input type="pseudo" id="pseudo" class="input" placeholder="Mettez votre pseudo" required>
        </div>


        <div class="input-group">
            <label for="age" class="group-label">Age</label>
            <input type="age" id="age" class="input" placeholder="Mettez votre age" required>
        </div>


        <div class="input-group">
            <label for="langue" class="group-label">Langue</label>
            <select type="langue" id="langue">
                <option value="">-- Sélectionnez votre langue  --</option>
                <option value="français">Français</option>
                <option value="anglais">Anglais</option>
                <option value="espagnol">Espagnol</option>
            </select>
        </div>

        <div class="input-group">
            <label for="style" class="group-label">Style de jeu</label>
            <select type="style" id="style">
                <option value="">-- Sélectionnez votre style de jeu --</option>
                <option value="casual">Casual - Joue de temps en temps</option>
                <option value="regular">Regular - Joue régulièrement</option>
                <option value="challenger">Challenger - Apprécie la difficulté</option>
                <option value="hardcore">Hardcore - Pousse toujours plus loin la difficulté</option>
                <option value="achivementH">Achivement hunter - Cherche à obtenir les succès</option>
                <option value="competitive">Competitive - Niveau élevé, participe à des tournois</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit">Inscription</button>
        </div>
    </form>

    

</div>