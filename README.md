# doctors-office-api

## À L'ATTENTION DES PROFFESSEURS

- Le dépot GIT du projet est ici : [`MaxenceMB/doctors-office-api`](https://github.com/MaxenceMB/doctors-office-api)   
- Les membres du groupe sont : Maxence MAURY-BALIT et Enzo LOUIS. Nous sommes le groupe B1.   
- Pour avoir un jeton depuis l'API Authentification, il faut utiliser le login `secretaire1` et le mot de passe `password1234!`.   
- Nous avons implémenté l'impossibilité de créer des consultations sur les jours fériés.   
- L'URL principale de l'API est la suivante : [`xouxou.alwaysdata.net/doctors-office-api/`](http://xouxou.alwaysdata.net/doctors-office-api/)   


# Readme du projet

Welcome to the **doctors-office-api** project, an api use with the [`enzolouis/doctors-office-website`](https://github.com/enzolouis/doctors-office-website) web application.
This project contains 2 APIs that doesn't communicate together.

## ⭐ Key Features

### 👥 Authentification API
The application provides an authentification api with login/password and token management.

### 👨 Cabinet API : Patient
The application provides comprehensive patient management, allowing add, remove and edit "patient" with basic informations.

### 👩‍⚕️ Cabinet API : Doctor ("Medecin")
The application provides comprehensive medecin management, allowing add, remove and edit "medecin" with basic informations.

### 📅 Cabinet API : Appointment
The application provides comprehensive consultation management, allowing schedule, remove and edit "consultation" with basic informations.

### 📊 Cabinet API : Statistics
The application provides statistics in GET for all the informations above.
