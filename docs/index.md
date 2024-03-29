## Description

Ce plugin permettant de gérer les appareils Yamaha compatible Musiccast.
Musiccast est une API dévellopé par Yamaha sur ces appareils.

Cette API permet pour chaque appareil:

> - de voir et les informations matériels.
> - de voir et modifier les informations logiciels (lecture des différentes zones, gestion net-radio, gestion réveil).
> - de recevoir un retour d’information à chauque mise à jour des informations logiciels.

## Installation

**jeedom** : Dans le menu : Plugins → Gestions des plugins → Cliquer sur « + Plugins »

* Type de source :  **GitHub**
* ID logique du plugin :  **YamahaMusiccast**
* Utiliser ou organisation du dépôt :  **lucguinchard**
* Nom du dépôt :  **plugin-Yamaha-musiccast**
* Branche :  **master**

## Configuration

> Après téléchargement du plugin, il faut l’activer.

La partie configuration du plugin permet le paramétrage du socket pour le dialogue entre les appareils Musiccast et (jee\|next)dom :

* Port du socket - permet de choisir le port UDP sur lequel les appareils Musiccast dialogueront.
* Nom de l’application (Ce paramétre sera surement supprimé)

### Ajout des équipements

> Il faut que vos appareils Musiccast soit dans le même réseau que votre jeedom.

La `Recherche automatique` permet de céer vos jeeobjects automatiquements.

## FAQ

### Mes appareils ne sont pas détectés avec la recherche automatique ?

> Vérifier que vos appareils Musiccast soit dans le même réseau que votre jeedom.

### Dans les logs il y a des messages du type :
```
L’appareil 192.168.x.y dialogue sur le port 9999 avec le message : ????????
```

> En effet les `Google Home` communiquent entre eux avec le port 9999. Cela ne perturbe pas le bon fonctionnement. Par contre il est possible de changer le port du plugin-Yamaha-musiccast dans la page de configuration pour qu’il n'utilise plus le port déjà utilisé (ici : 9999).