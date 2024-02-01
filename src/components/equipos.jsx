import React, { useState } from 'react';
import '../styleSheets/Equipos.css';

const Equipos = () => {
  // Estado para almacenar la lista de equipos
  const [equipos, setEquipos] = useState([
    { id: 1, nombre: 'Microscopio', categoria: 'Física', cantidad: 5, estado: 'Disponible' },
    { id: 2, nombre: 'Prismáticos', categoria: 'Física', cantidad: 3, estado: 'En Reparación' },
    { id: 3, nombre: 'Telescopio', categoria: 'Física', cantidad: 8, estado: 'Disponible' },
    { id: 4, nombre: 'Acelerador de Partículas', categoria: 'Física', cantidad: 1, estado: 'En Reparación' },

    { id: 5, nombre: 'Microscopio Electrónico', categoria: 'Química', cantidad: 2, estado: 'En Reparación' },
    { id: 6, nombre: 'Espectrómetro de Masas', categoria: 'Química', cantidad: 4, estado: 'Disponible' },
    { id: 7, nombre: 'Centrífuga', categoria: 'Química', cantidad: 6, estado: 'Disponible' },
    { id: 8, nombre: 'Reactores Químicos', categoria: 'Química', cantidad: 2, estado: 'En Reparación' },

    { id: 9, nombre: 'Hormigonera', categoria: 'Ingeniería Civil', cantidad: 7, estado: 'Disponible' },
    { id: 10, nombre: 'Nivel Láser', categoria: 'Ingeniería Civil', cantidad: 2, estado: 'En Reparación' },
    { id: 1, nombre: 'Microscopio', categoria: 'Física', cantidad: 5, estado: 'Disponible' },
    { id: 2, nombre: 'Prismáticos', categoria: 'Física', cantidad: 3, estado: 'En Reparación' },
    { id: 3, nombre: 'Telescopio', categoria: 'Física', cantidad: 8, estado: 'Disponible' },
    { id: 4, nombre: 'Acelerador de Partículas', categoria: 'Física', cantidad: 1, estado: 'En Reparación' },

    { id: 5, nombre: 'Microscopio Electrónico', categoria: 'Química', cantidad: 2, estado: 'En Reparación' },
    { id: 6, nombre: 'Espectrómetro de Masas', categoria: 'Química', cantidad: 4, estado: 'Disponible' },
    { id: 7, nombre: 'Centrífuga', categoria: 'Química', cantidad: 6, estado: 'Disponible' },
    { id: 8, nombre: 'Reactores Químicos', categoria: 'Química', cantidad: 2, estado: 'En Reparación' },

    { id: 9, nombre: 'Hormigonera', categoria: 'Ingeniería Civil', cantidad: 7, estado: 'Disponible' },
    { id: 10, nombre: 'Nivel Láser', categoria: 'Ingeniería Civil', cantidad: 2, estado: 'En Reparación' },
    { id: 11, nombre: 'Máquina de Ensayos de Materiales', categoria: 'Ingeniería Civil', cantidad: 3, estado: 'Disponible' },
    { id: 12, nombre: 'Tren de Compactación', categoria: 'Ingeniería Civil', cantidad: 1, estado: 'En Reparación' },

    { id: 13, nombre: 'Computadora de Alto Rendimiento', categoria: 'Ingeniería Informática', cantidad: 10, estado: 'Disponible' },
    { id: 14, nombre: 'Impresora 3D', categoria: 'Ingeniería Informática', cantidad: 3, estado: 'En Reparación' },
    { id: 15, nombre: 'Servidor de Red', categoria: 'Ingeniería Informática', cantidad: 5, estado: 'Disponible' },
    { id: 16, nombre: 'Portátil de Desarrollo', categoria: 'Ingeniería Informática', cantidad: 2, estado: 'En Reparación' },

    { id: 17, nombre: 'Torno CNC', categoria: 'Máquinas', cantidad: 4, estado: 'Disponible' },
    { id: 18, nombre: 'Fresadora Universal', categoria: 'Máquinas', cantidad: 2, estado: 'En Reparación' },
    { id: 19, nombre: 'Sierra Circular', categoria: 'Máquinas', cantidad: 3, estado: 'Disponible' },
    { id: 20, nombre: 'Prensa Hidráulica', categoria: 'Máquinas', cantidad: 1, estado: 'En Reparación' },
  ]);

  return (
    <div className='contenido-pagina'>
      <h2>Equipos</h2>

      <table className="tabla-equipos">
        <thead>
          <tr>
            <th>ID del Producto</th>
            <th>Nombre del Producto</th>
            <th>Categoría</th>
            <th>Número de Artículos Disponibles</th>
            <th>Estado del Producto</th>
          </tr>
        </thead>
        <tbody>
          {equipos.map((equipo) => (
            <tr key={equipo.id} className="equipo-item">
              <td>{equipo.id}</td>
              <td>{equipo.nombre}</td>
              <td>{equipo.categoria}</td>
              <td>{equipo.cantidad}</td>
              <td>{equipo.estado}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default Equipos;