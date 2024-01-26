import { Calendar, dayjsLocalizer } from 'react-big-calendar';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import dayjs from 'dayjs';

function Calendario() {
  const localizer = dayjsLocalizer(dayjs)
  return (
    <div className='contenido-pagina'>
      <div className='calendario' style={{
      height:'90vh',
      width:'70vw'

    }}>
      <Calendar
      localizer={localizer}
      
    />
    </div>
    </div>
    
  );
}

export default Calendario;

