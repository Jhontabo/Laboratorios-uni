import pool from '../config/db.js';

export async function login(req, res) {
    const { correo, password } = req.body;
    const consult = 'SELECT * FROM usuarios WHERE correo = ?';


    try {
        const [rows] = await pool.query(consult, [correo]);

        if (rows.length === 0) {
            console.log('Usuario no encontrado');
            return res.status(401).json({ message: 'Usuario no encontrado' });
        }

        const user = rows[0];
        console.log('Usuario encontrado:', user);

        // Comparar directamente la contraseña en texto plano
        if (user.password !== password) {
            console.log('Contraseña incorrecta');
            return res.status(401).json({ message: 'Contraseña incorrecta' });
        }


    } catch (error) {
        console.error('Error en el login:', error.message);
        res.status(500).json({ message: 'Error interno del servidor', error: error.message });
    }
}
