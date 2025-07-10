using System;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Data;
using System.Threading.Tasks;

namespace library
{
    //Codigo Omar

    public class ENReserva
    {
        public ENUsuario usuario { get; set; }
        public ENVideojuego videojuego { get; set; }
        public int id { get; set; }
        public DateTime fecha { get; set; }
        public DateTime fechaEntrega { get; set; }
        public double pagado;
        private CADReserva reserva { get; set; }

        public ENReserva()
        {
            this.usuario = new ENUsuario();
            this.videojuego = new ENVideojuego();
            this.fecha = DateTime.Now;
            this.fechaEntrega = DateTime.Now;
            this.pagado = 0;
            this.reserva = new CADReserva();
        }

        public ENReserva(ENUsuario u, ENVideojuego v, DateTime fecha, DateTime fechaEntrega, double pagado)
        {
            this.usuario = u;
            this.videojuego = v;
            this.fecha = fecha;
            this.fechaEntrega = fechaEntrega;
            this.pagado = pagado;
            this.reserva = new CADReserva();
        }

        public bool createReserva()
        {
            return this.reserva.createReserva(this);
        }
        public bool deleteReserva()
        {
            return this.reserva.deleteReserva(this);
        }
        public bool updateReserva()
        {
            return this.reserva.updateReserva(this);
        }
        public bool readReserva()
        {
            return this.reserva.readReserva(this);
        }

        public bool existeReserva()
        {
            return this.reserva.existeReserva(this);
        }

        public DataTable mostrarReservas()
        {
            return this.reserva.mostrarReservas(this);
        }
    }

}
