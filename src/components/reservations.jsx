import { Calendar, dayjsLocalizer } from 'react-big-calendar';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import 'react-big-calendar/lib/addons/dragAndDrop/styles.css';
import dayjs from 'dayjs';
import 'dayjs/locale/es'

dayjs.locale('es');

function Calendario() {
  const localizer = dayjsLocalizer(dayjs)


  const events = [
    {
      start:dayjs('2024-01-25T12:00:00').toDate(),
      end:dayjs('2024-01-25T13:00:00').toDate(),
      title:'reunion docentes'
    },
    {
      start:dayjs('2024-01-26T14:00:00').toDate(),
      end:dayjs('2024-01-26T16:00:00').toDate(),
      title:'entregar reporte'
    },

    {
      start:dayjs('2024-01-29T17:00:00').toDate(),
      end:dayjs('2024-01-29T20:00:00').toDate(),
      title:'clase fisica salon 602'
    }
  ]


  return (
    <div className='contenido-pagina'>
      <div className='calendario' style={{
      height:'90vh',
      width:'70vw'

    }}>
      <Calendar
     messages = {{
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
      }
     }
    
      localizer={localizer}
      events={events}
      views={['month','week','day','agenda']}
      defaultView='month'  
      toolbar={true}    
    />
    </div>
    </div>
    
  );
}

export default Calendario;

