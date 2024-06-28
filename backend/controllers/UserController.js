import { getUsuarioPorCorreo, getUsuarios } from '../models/userModel.js';

// Controlador para mostrar todos los usuarios
export async function getAllUsuarios(req, res) {
  try {
    const usuarios = await getUsuarios();
    res.json(usuarios);
  } catch (error) {
    res.status(500).json({ message: 'Error al obtener usuarios', error: error.message });
  }
}

// Controlador para mostrar un usuario por correo electrónico
export async function getUsuarioByCorreo(req, res) {
  try {
    const correo = req.params.correo;
    const usuario = await getUsuarioPorCorreo(correo);

    if (!usuario) {
      return res.status(404).json({ message: 'Usuario no encontrado' });
    }

    res.json(usuario);
  } catch (error) {
    res.status(500).json({ message: 'Error al obtener el usuario', error: error.message });
  }
}
