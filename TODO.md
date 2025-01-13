# Automatisation du développement
## Cours 2 - TD

### Préalable
Comme pour la semaine dernière, utiliser les principes gitflow pour ce projet, nommer correctement vos branches et vos commits ([conventional commits](https://www.conventionalcommits.org/en/v1.0.0/), [gitmoji](https://gitmoji.dev/) ou autre).

### Objectif
- [x] Installer et lancer le projet
- [x] Modifier `PopulateDatabaseCommand.php` pour ajouter des données aléatoire cohérentes (2-4 sociétés avec 2-3 bureaux et une dizaine d'employés) 
- [x] Ajouter Phpcs et Phpstan au projet
- [x] Ajouter ESLint au projet
- [ ] Deplacer le js et css dans un dossiers `assets` à la racine du projet, et les build dans un dossier `./public/build` grâce à vite

### Bonus
- [ ] Ajouter des git hooks pour lancer phpcs, phpstan et eslint avant chaque commit et/ou push
- [ ] Utiliser la version serveur de vite pour le développement
- [ ] Changer le liens des assets dans twig en fonction de la variable d'environnement `APP_ENV` (dev ou prod)
