# Dolphin votre compagnon pour ne pas vous perdre dans un océan de question ! 🐬

## Installation de dolphin
### Pré-requis

- Installer PHP 8.1 : https://www.php.net/downloads.php
- Installer Node 16.15+: https://nodejs.org/en/download/
- Installer Composer 2.1+ : https://getcomposer.org/
- Installer Postgres SQL : https://www.postgresql.org/
- Installer un outil de visualisation de base de donnée ( TablePlus, DBeaver, Navicat... )

### Configuration

- Cloner le projet : 
```
cd emplacement
git clone https://github.com/sulyaniggui/dolphin.git
```
  


- Installer le Symfony CLI : https://symfony.com/download

#### First of all moussaillon on récupère les packages qui nous sont utiles grâce à 2 commandes successives qui sont les suivantes :
```
- composer install
- npm install
```

#### Créer un fichier .env.local 
Éditer dedans votre connexion à la base de donnée en remplaçant par les bonnes informations

```
 DATABASE_URL="postgressql://symfony:ChangeMe@127.0.0.1:5432/app?serverVersion=13&charset=utf8"
```

#### Créer la base de donnée avec la commande suivante :
````
- php bin/console doctrine:database:create
````

#### Envoyez la migration sur la base de donnée avec la commande suivante :
```
- php bin/console doctrine:migrations:migrate
```

#### Une fois toute la base de donnée bien formé nous voulons lui ajouter des datas pour simuler le rendu pour cela nous ferons la commande suivante :
```
- symfony console doctrine:fixtures:load
```

#### Une fois toutes les lignes faites c'est le moment de rentrer dans le grand bain et de vous laissez guider par le dolphin pour cela taper la commande suivante :`
```
- symfony server:start
```

### PS: N'oubliez pas d'ouvrir un deuxième terminal et de faire un npm run build sinon vous ne verrez pas l'océan avec les bonnes lunettes si vous voyz ce que je veux dire. 😉
  
### Test

#### Connexion utilisateur
    Identifiant : 'david'
    Password : '1234'

#### Connexion administrateur
    Identifiant : 'sulyan'
    Password : '1234'
