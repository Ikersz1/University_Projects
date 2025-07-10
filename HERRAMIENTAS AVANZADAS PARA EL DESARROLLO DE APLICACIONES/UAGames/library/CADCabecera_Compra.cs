using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Configuration;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Globalization;

namespace library
{

    class CADCabecera_Compra
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADCabecera_Compra()
        {
            conexionBBDD = "Data Source=(LocalDB)\\MSSQLLocalDB;AttachDbFilename=|DataDirectory|\\Database.mdf;Integrated Security=True;";
            c = new SqlConnection(conexionBBDD);
        }

        public bool createCabecera(ENCabecera_Compra cabecera)
        {
            bool result = true;
            try
            {
                this.c.Open();
                string fechaFormatoCorrecto = cabecera.fecha.ToString("yyyy-MM-dd HH:mm:ss");
                string total = cabecera.totalCompra.ToString().Replace(',','.');
                string query = "INSERT INTO Compra (fecha,total,usuarioID) VALUES (CONVERT(datetime, '" + fechaFormatoCorrecto + "', 120),"+total+"," + cabecera.usuario.id +");";
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
        public bool deleteCabecera(ENCabecera_Compra cabecera)
        {
                bool result = true;
                try
                {
                    c.Open();
                    string query = "DELETE FROM Compra WHERE id LIKE '" + cabecera.id + "';";
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
        public bool updateCabecera(ENCabecera_Compra cabecera)
        {
            bool result = true;
            try
            {
                c.Open();
                string query = "UPDATE Compra SET " +
                    "total=" + cabecera.totalCompra + " WHERE id LIKE '" + cabecera.id + "';";
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
        public bool readCabecera(ENCabecera_Compra cabecera)
        {
            bool result = true;
            try
            {
                this.c.Open();
                string query = "SELECT * FROM Compra WHERE id = " + cabecera.id + ";";
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader reader = com.ExecuteReader();
                if (reader.Read())
                {
                    cabecera.totalCompra = Double.Parse(reader["total"].ToString());
                    // Si queremos saber el cliente y la fecha del pedido añadir aqui
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
