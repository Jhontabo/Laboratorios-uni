// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyAaG7K9Y1m2n0OEK-3iN1yxSzFcvKYFxGU",
    authDomain: "login-react-e2981.firebaseapp.com",
    projectId: "login-react-e2981",
    storageBucket: "login-react-e2981.appspot.com",
    messagingSenderId: "480365832294",
    appId: "1:480365832294:web:c662838ec4cc0f9ed665a7"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
export const auth = getAuth(app)