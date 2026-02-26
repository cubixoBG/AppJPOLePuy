Notice rapide

🧬 cloner le dépôt : 
```
git clone 
```

🔨 build les containers : 
```
docker compose up --build 
```

⚠️ Les containers peuvent prendre du temps a installer leurs dépendances la première fois !

Mais il n'est plus nécessaire d'avoir les node_modules / vendor en local ils sont téléchargés dans les containers au build (composer i | npm i)

# AppJPOLePuy
📱 Projet en Binôme JPO IUT Le Puy : Application double pour fluidifier l'accueil. Une interface borne pour l'enregistrement rapide des visiteurs (MMI, Info, Chimie) et une interface ambassadeur permettant aux étudiants de collecter les retours via des questionnaires de satisfaction. Optimisez vos journées portes ouvertes ! 🚀

🚀 Projet JPO IUT Le Puy-en-Velay
Ce projet vise à moderniser et fluidifier la gestion des Journées Portes Ouvertes (JPO) de l'IUT du Puy-en-Velay. Il se compose de deux applications distinctes développées pour répondre aux besoins des visiteurs, des étudiants ambassadeurs et de l'administration.

🎯 Objectifs du Projet
Numériser l'accueil des visiteurs pour éviter les files d'attente papier.

Centraliser les données de visite pour les départements (MMI, Informatique, Chimie).

Recueillir en temps réel les retours d'expérience via des questionnaires de satisfaction.

📱 Les Applications
1. 🧑‍💻 App Visiteur (Borne d'accueil)
Destinée à être installée sur des tablettes à l'entrée de l'IUT ou de chaque département.

Enregistrement rapide : Nom, prénom, établissement actuel (ou dernier établissement fréquenté), formation visée.

RGPD Compliant : Consentement explicite pour la conservation des données.

2. 🎓 App Ambassadeur (Étudiants)
Utilisée par les étudiants sur smartphone ou tablette lors des visites guidées.

Questionnaire de satisfaction : Formulaire rapide pour évaluer la présentation et l'intérêt des visiteurs.

Outil d'aide à la présentation : (Optionnel) Fiches techniques des formations.

🛠️ Fonctionnalités Futures / Idées
[ ] Génération de QR Code pour un accès rapide au questionnaire.

[ ] Tableau de bord administrateur avec présentation des donnée. Et possibilité d'appeler un étudiant quand un visiteur arrive.

💻 Stack Technique
Frontend : [React]

Backend : [Symfony]

Base de données : [MySQL, PhPMyAdmin]
