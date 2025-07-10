// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";
import { getFirestore } from "firebase/firestore";
import { getStorage } from "firebase/storage";

// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyAzRmlguapUfUgztnPhSYWpQazPE087S6g",
  authDomain: "taes-67377.firebaseapp.com",
  projectId: "taes-67377",
  storageBucket: "taes-67377.firebasestorage.app",
  messagingSenderId: "949599075738",
  appId: "1:949599075738:web:aa82cd7100b7c163d68e23",
  measurementId: "G-0F5PZFWS7D"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

export { auth, db, storage};