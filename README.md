# Doctors Office API

## À L'ATTENTION DES PROFESSEURS

- Le dépot GIT du projet est ici : [`MaxenceMB/doctors-office-api`](https://github.com/MaxenceMB/doctors-office-api)   
- Les membres du groupe sont : Maxence MAURY-BALIT et Enzo LOUIS. Nous sommes le groupe B1.   
- Pour avoir un jeton depuis l'API Authentification, il faut utiliser le login `secretaire1` et le mot de passe `password1234!`.   
- Nous avons implémenté l'impossibilité de créer des consultations sur les jours fériés.   
- L'URL principale de l'API est la suivante : [`xouxou.alwaysdata.net/doctors-office-api/`](http://xouxou.alwaysdata.net/doctors-office-api/)    


# Readme du projet

Welcome to the **doctors-office-api** project, an api made for an university course to complete the [`enzolouis/doctors-office-website`](https://github.com/enzolouis/doctors-office-website) web application.   
This project contains 2 APIs:    
- API Auth (For the authentification)   
- API Cabinet Médical (The API made for the website)   

A full (but in french) documentation on both APIs is available at this link : [`Documentation API Cabinet Médical`](https://documenter.getpostman.com/view/32827479/2sA35MxJZx)     


## 🔑 API Auth
Link: [`doctors-office-api/auth`](http://xouxou.alwaysdata.net/doctors-office-api/auth)   
The application provides an authentification api with login/password and token management.   
It gives a token valid for a day if the account given is correct.
HTTP Methods: `GET` to verify a token given in the url, `POST` to give a login and a password in the body and get the token back.    

## API Cabinet Médical
### 👨 Patient
Link: [`doctors-office-api/usagers`](http://xouxou.alwaysdata.net/doctors-office-api/usagers)   
The API provides comprehensive patient management, allowing add, remove and edit "patient" with basic informations like name, surname, gender, birthdate etc...       
HTTP Methods: `GET` to get all or one patient(s), `POST` to add a patient, `PUT` & `PATCH` to edit a patient and `DELETE` to delete a patient.   

### 👩‍⚕️ Doctor ("Medecin")
Link: [`doctors-office-api/medecins`](http://xouxou.alwaysdata.net/doctors-office-api/medecins)   
The API provides comprehensive medecin management, allowing add, remove and edit "medecin" with basic informations such as name and surname.     
HTTP Methods: `GET` to get all or one doctor(s), `POST` to add a doctor, `PUT` & `PATCH` to edit a doctor and `DELETE` to delete a doctor.   

### 📅 Appointment ("Consultation")
Link: [`doctors-office-api/consultations`](http://xouxou.alwaysdata.net/doctors-office-api/consultations)   
The API provides comprehensive consultation management, allowing schedule, remove and edit "consultation" with basic informations like the doctor and the patient, the date etc...
HTTP Methods: `GET` to get all or one appointment(s), `POST` to add an appointment, `PUT` & `PATCH` to edit an appointment and `DELETE` to delete an appointment.   

### 📊 Statistics
Link (Doctors) : [`doctors-office-api/stats/medecins`](http://xouxou.alwaysdata.net/doctors-office-api/stats/medecins)   
Link (Patients): [`doctors-office-api/stats/usagers`](http://xouxou.alwaysdata.net/doctors-office-api/stats/usagers)   
The API provides statistics in GET for doctors and patients.   
- For Doctors, it gives a ranking off the most busy doctors, calculated with their appointment durations.   
- For Patients, it gives the number of patients per age and gender.   
HTTP Methods: `GET` to get all the stats, corresponding to the link used.   

---

#### Disclaimer
The API is fully finished but we didn't implement it in the website. For now, the project stands for itself.   
You can try it using a client such as *Postman* for example.
