using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    class CADLinea_Compra
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADLinea_Compra()
        {
            conexionBBDD = "Data Source=(LocalDB)\\MSSQLLocalDB;AttachDbFilename=|DataDirectory|\\Database.mdf;Integrated Security=True;";
            c = new SqlConnection(conexionBBDD);
        }

        public bool createLinea(ENLinea_Compra linea, int idcabecera)
        {
            bool result = true;
            try
            {
                this.c.Open();
                string importe = linea.importe.ToString().Replace(",", ".");
                string query = "INSERT INTO LineasCompra (importe, videojuegoID, cantidad, compraID) VALUES (" + importe + ","+ linea.videojuego.Id + ","+linea.cantidad+"," + idcabecera+ ");";
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
            }
            catch (SqlException ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            finally
            {
                this.c.Close();
            }

            return result;
        }
        public bool deleteLinea(ENLinea_Compra linea)
        {
            bool result = true;
            try
            {
                c.Open();
                string query = "DELETE FROM LineasCompra WHERE id LIKE '" + linea.id + "';";
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
            }
            catch (SqlException ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            finally
            {
                this.c.Close();
            }
            return result;
        }
        public bool updateLinea(ENLinea_Compra linea, ENVideojuego videojuego)
        {
            bool result = true;
            try
            {
                c.Open();
                string query = "UPDATE LineasCompra SET " +
                    "videojuegoID=" + videojuego.Id + " WHERE id = " + linea.id + ";";
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
            }
            catch (SqlException ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            finally
            {
                this.c.Close();
            }
            return result;
        }
        public bool readLinea(ENLinea_Compra linea, ENVideojuego videojuego)
        {
            bool result = true;
            try
            {
                this.c.Open();
                string query = "SELECT * FROM LineasCompra WHERE id = " + linea.id + ";";
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader reader = com.ExecuteReader();
                if (reader.Read())
                {
                    linea.cantidad = int.Parse(reader["cantidad"].ToString());
                    linea.importe = Double.Parse(reader["importe"].ToString());
                    // Si queremos saber el videojuego y cabecera del pedido añadir aquí
                }
                else
                {
                    result = false;
                }
                reader.Close();
            }
            catch (SqlException ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                result = false;
            }
            finally
            {
                this.c.Close();
            }
            return result;
        }
    }
}
