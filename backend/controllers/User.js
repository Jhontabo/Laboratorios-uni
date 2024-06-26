// Lo primero es importar el modelo que desarrollamos en este caso es UserModel

import { getUsuarioPorCorreo, getUsuarios } from '../models/userModel.js';


//Creamos una funcion para obtener todos los usuaarios

export async function getAllUsuarios(req, res) {
  try {
    const usuarios = await getUsuarios();
    res.json(usuarios);
  } catch (error) {
    res.status(500).json({ message: 'Error al obtener usuarios', error });
  }
}

export async function getUsuarioByCorreo(req, res) {
  try {
    const correo = req.params.correo; // Usa req.params si el correo es parte de la URL (p. ej., /usuarios/:correo)
    // const correo = req.query.correo; // Usa req.query si el correo es un parámetro de consulta (p. ej., /usuarios?correo=...)
    const usuario = await getUsuarioPorCorreo(correo);

    // Si el usuario no se encuentra
    if (!usuario) {
      return res.status(404).json({ message: 'Usuario no encontrado' });
    }

    // Si el usuario se encuentra, devolverlo en la respuesta
    res.json(usuario);
  } catch (error) {
    // Manejo de errores
    res.status(500).json({ message: 'Error al obtener el usuario', error: error.message });
  }
}