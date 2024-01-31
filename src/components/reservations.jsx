import React, { useState } from 'react';
import { Calendar, dayjsLocalizer } from 'react-big-calendar';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import 'react-big-calendar/lib/addons/dragAndDrop/styles.css';
import dayjs from 'dayjs';
import 'dayjs/locale/es';
import '../styleSheets/calendar.css';
import { Button, Modal } from 'react-bootstrap';

dayjs.locale('es');

function Calendario() {
  const localizer = dayjsLocalizer(dayjs);
  const [showModal, setShowModal] = useState(false);

  const handleOpenModal = () => setShowModal(true);
  const handleCloseModal = () => setShowModal(false);

  const events = [
    {
      start: dayjs('2024-01-25T12:00:00').toDate(),
      end: dayjs('2024-01-25T13:00:00').toDate(),
      title: 'reunion docentes',
    },
    {
      start: dayjs('2024-01-26T14:00:00').toDate(),
      end: dayjs('2024-01-26T16:00:00').toDate(),
      title: 'entregar reporte',
    },
    {
      start: dayjs('2024-01-29T17:00:00').toDate(),
      end: dayjs('2024-01-29T20:00:00').toDate(),
      title: 'clase fisica salon 602',
    },
  ];

  return (
    
    <div className='contenido-pagina'>
      <div className='calendario'>
        <Button variant="primary" onClick={handleOpenModal}>
          Nueva Actividad
        </Button>

        <Modal show={showModal} onHide={handleCloseModal}>
          <Modal.Header closeButton>
            <h1 className="modal-title fs-5" id="exampleModalLabel">
              Crear nueva Actividad
            </h1>
          </Modal.Header>
          <Modal.Body>
            <form>
              <div className="mb-3">
                <label htmlFor="recipient-name" className="col-form-label">
                  Hora de inicio
                </label>
                <input type="text" className="form-control" id="recipient-name" />
              </div>
              <div className="mb-3">
                <label htmlFor="message-text" className="col-form-label">
                  Hora de finalizacion
                </label>
                <textarea className="form-control" id="message-text"></textarea>
              </div>

              <div className="mb-3">
                <label htmlFor="message-text" className="col-form-label">
                  Fecha
                </label>
                <textarea className="form-control" id="message-text"></textarea>
              </div>
            </form>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="secondary" onClick={handleCloseModal}>
              Cerrar
            </Button>
            <Button variant="primary">Guardar</Button>
          </Modal.Footer>
        </Modal>

        <Calendar
          localizer={localizer}
          events={events}
          views={['month', 'week', 'day']}
          defaultView='month'
          toolbar={true}
          messages={{
            today: 'Hoy',
            previous: 'Anterior',
            next: 'Siguiente',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            agenda: 'Agenda',
            date: 'Fecha',
            time: 'Hora',
            event: 'Evento',
            allDay: 'Todo el día',
            showMore: total => `+${total} más`,
          }}
        />
      </div>
    </div>
  );
}

export default Calendario;
