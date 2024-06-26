//Importamos la base de datos de la sigueinte manera 
import db from '../database/db.js';


export async function getUsuarios() {
  try {
    const [rows] = await db.query('SELECT * FROM usuarios');
    return rows;
  } catch (err) {
    console.error('Error al obtener usuarios:', err);
    throw err;
  }
}

export async function getUsuarioPorCorreo(correo) {
  try {
    const [rows] = await db.query('SELECT * FROM usuarios WHERE correo = ?', [correo]);
    return rows[0];
  } catch (err) {
    console.error('Error al obtener usuario por correo:', err);
    throw err;
  }
}
