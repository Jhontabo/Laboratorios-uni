import { Calendar, dayjsLocalizer } from 'react-big-calendar';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import dayjs from 'dayjs';

function Calendario() {
  const localizer = dayjsLocalizer(dayjs)
  return (
    <div style={{
      height:'90vh',
      width:'70vw'

    }}>
      <Calendar
      localizer={localizer}
      
    />
    </div>
  );
}

export default Calendario;

