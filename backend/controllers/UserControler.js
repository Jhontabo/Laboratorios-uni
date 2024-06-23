import { getUsuarioPorCorreo, getUsuarios } from '../models/userModel.js';

//controlador para mostrar todos los usuarios
export async function getAllUsuarios(req, res) {
  try {
    const usuarios = await getUsuarios();
    res.json(usuarios);
  } catch (error) {
    res.status(500).json({ message: 'Error al obtener usuarios', error });
  }
}

//controlador para mostrar solo usuario por correo electronico
export async function getUsuarioByCorreo(req, res) {
    
    try {
      const correo = req.params.correo;
      const usuario = await getUsuarioPorCorreo(correo);
      console.log("esto es el usuario: "+usuario.id_rol);
      if (!usuario) {
        return res.status(404).json({ message: 'Usuario no encontrado' });
      }
      res.json(usuario);
    } catch (error) {
      //console.log(usuario);
      res.status(500).json({ message: 'Error al obtener el usuario', error });
    }
  }