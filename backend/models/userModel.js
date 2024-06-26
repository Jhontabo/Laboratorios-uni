import pool from '../config/db.js';

//modelo de sentencia para consulta asia la base de datos
export async function getUsuarios() {
  const [rows] = await pool.query('SELECT * FROM usuarios');
  return rows[0];

}

export async function getUsuarioPorCorreo(correo) {
  const [rows] = await pool.query('SELECT * FROM usuarios WHERE correo = ?', [correo]);
  return rows[0];
}


console.log(getUsuarios())