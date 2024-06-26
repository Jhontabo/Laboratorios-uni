import express from 'express';
import { getAllUsuarios } from '../controllers/User.js';
import { getUsuarioByCorreo } from '../controllers/User.js';


const router = express.Router();

router.get('/usuarios', getAllUsuarios);
router.get('/usuarios/:correo', getUsuarioByCorreo);


export default router;
