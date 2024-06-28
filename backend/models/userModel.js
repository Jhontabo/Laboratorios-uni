import pool from '../config/db.js';

//modelo de sentencia para consulta hacia la base de datos

//Funcion para obtener todos los usuarios
export async function getUsuarios() {

  try {

    const [rows] = await pool.query('SELECT * FROM usuarios');
    return rows;

  } catch (error) {
    console.log(error);
  }

}

export async function getUsuarioPorCorreo(correo) {

  try {
    const [rows] = await pool.query('SELECT * FROM usuarios WHERE correo = ?', [correo]);
    return rows[0];
  } catch (error) {

    console.log(error)
  }

}
