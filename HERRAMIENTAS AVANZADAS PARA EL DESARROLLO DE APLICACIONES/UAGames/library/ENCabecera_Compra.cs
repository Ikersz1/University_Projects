using System;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENCabecera_Compra 
    {
        public ENUsuario usuario { get; set; }
        public int id { get; set; }
        public DateTime fecha { get; set; }
        public double totalCompra { get; set; }
        private CADCabecera_Compra cabecera { get; set; }

        public ENCabecera_Compra()
        {
            this.usuario = new ENUsuario();
            this.fecha = DateTime.Now;
            this.cabecera = new CADCabecera_Compra();
        }
        public ENCabecera_Compra(ENUsuario usuario, DateTime fecha, double total)
        {
            this.usuario = usuario;
            this.fecha = fecha;
            this.totalCompra = total;
        }

        // Revisar todo esto y cambiarlo por crear un CAD antes de insertar
        public bool createCabecera_Compra()
        {
            CADCabecera_Compra cabecera = new CADCabecera_Compra();
            return cabecera.createCabecera(this);
        }
        public bool deleteCabecera_Compra()
        {
            return this.cabecera.deleteCabecera(this);
        }
        public bool updateCabecera_Compra()
        {
            return this.cabecera.updateCabecera(this);
        }
        public bool readCabecera_Compra()
        {
            return this.cabecera.readCabecera(this);
        }
    }

}
