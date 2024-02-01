import React from 'react';
import '../styleSheets/chat.css'; 


const Chat = () => {
  const handleWhatsAppClick = () => {
    const whatsappLink = "https://wa.me/+573235937501";
    window.location.href = whatsappLink;
  };

  return (
    <div className='contenido-pagina'>
      <h2>Chat</h2>
      <button className="whatsapp-btn" onClick={handleWhatsAppClick}>
        Iniciar Conversación en WhatsApp
      </button>
    </div>
  );
};

export default Chat;
