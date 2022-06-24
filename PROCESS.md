## Process de création de Dolphin ✨

### Installation des dépendances Symfony
- fakerphp/fake
- symfony/maker-bundle
- symfony/security
- doctrine/orm
- doctrine/doctrine-fixtures-bundle

### Création des entités
 #### Création de 6 entités
- Création de l'entité User
- Création de l'entité Category
- Création de l'entité Ticket et de ses relations à User et Category
- Création de l'entité Comment et de ses relations à User et à Ticket
- Création de l'entité Vote et de ses relations à Ticket, à Comment et à User
- Création de l'entité Report et de ses relations à Ticket, à Comment et à User

### Création des fixtures
- Initialisation des fixtures correspondant aux entités pour pouvoir bourrer la base de donnée d'informations.

### Création des controllers
#### Création de 4 controllers
- FrontController: Il me permet de gérer toutes les routes utiles pour la navigation des utilisateurs qu'ils soient connectés ou non.
- AuthenticationController : Il permet de gérer les routes liés à l'authentification ( connexion, authentification et déconnexion )
- UserController : Il me permet de gérer les routes utilisés par les utilisateus qui ont un compte utilisateur sur la plateforme.
- AdminController : Il permet de gérer les routes liés à l'administrateur. Toutes les routes qui vont lui permettre d'avoir des intéractions propre à son rôle d'administrateur.

### Installation de la dépendance form utile dans les controllers
- symfony/form
Définition de bootstrap en thème de formulaire par défaut

### Utilisation des repository
- Mise en place de 6 repository.
Chaque repository est là pour aller chercher des informations spécifique celon l'entity par rapport au besoin dans le controller.

Exemple d'une fonction dans le repository Ticket. La function findByCategory qui me sert à retrouver tous les tickets par rapport au slug de la catégory créée

### Mise en place du security
- Création d'un dossier Security
- Création d'un fichier dans ce dossier "CustomAuthenticator.php"
- Ce fichier est relié au config/packages/security.yaml
- Dans ce fichier on retrouve des règles présentes avant la redirection vers la connexion.
Exemple d'une règle : On ne peut pas se connecter à son compte utilisateur si le compte est desactivé par l'administrateur.
  
### Installation des dépendances front
- webpack-encore
- bootstrap

### Création des templates Twig
- Création de 4 dossiers templates correspondant à chaque controller
- Dans chaque fichier création du responsive de la webApp grâce à bootstrap
- Création du script front permettant la gestion des fonctionnalités upvote, downvote et report.



### Création du README
