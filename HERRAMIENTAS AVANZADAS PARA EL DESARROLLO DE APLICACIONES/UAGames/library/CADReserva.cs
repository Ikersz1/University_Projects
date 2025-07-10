using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Configuration;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Globalization;
using System.Data;

namespace library
{
    public class CADReserva
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADReserva()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = new SqlConnection(conexionBBDD);
        }

        public bool createReserva(ENReserva reserva)
        {
            bool result = true;
            try
            {
                this.c.Open();
                string query = "INSERT INTO Reserva (fecha, fechaEntrega, pagado, usuarioID, videojuegoID) VALUES (@fecha, @fechaEntrega, @pagado, @usuarioID, @videojuegoID)";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@fecha", reserva.fecha.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@fechaEntrega", reserva.videojuego.FechaLanzamiento.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@pagado", reserva.pagado);
                com.Parameters.AddWithValue("@usuarioID", reserva.usuario.id);
                com.Parameters.AddWithValue("@videojuegoID", reserva.videojuego.Id);
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


        public bool deleteReserva(ENReserva reserva)
        {
            bool result = true;
            try
            {
                c.Open();
                string query = "DELETE FROM Reserva WHERE videojuegoID = (select id from videojuego v where v.titulo = @titulo);";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@titulo", reserva.videojuego.Titulo);
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
                c.Close();
            }
            return result;
        }

        public bool existeReserva(ENReserva reserva)
        {
            bool result = true;

            try
            {
                c.Open();
                string query = "Select 1 from reserva r where r.videojuegoID = (select id from videojuego v where v.titulo = @titulo) and r.usuarioID = @usuarioID;";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@titulo", reserva.videojuego.Titulo);
                com.Parameters.AddWithValue("@usuarioID", reserva.usuario.id);
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
                c.Close();
            }
            return result;
        }


        public bool updateReserva(ENReserva reserva)
        {
            bool result = true;

            try
            {
                c.Open();
                string query = "UPDATE Reserva SET pagado = @pagado WHERE videojuegoID = (select id from videojuego v where v.titulo = @titulo);";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@titulo", reserva.videojuego.Titulo);
                com.Parameters.AddWithValue("@pagado", reserva.pagado);
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
                c.Close();
            }
            return result;
        }

        public bool readReserva(ENReserva reserva)
        {
            bool leido = false;
            SqlConnection connection = null;
            SqlDataReader dr = null;
            try
            {
                connection.Open();
                string query = "SELECT * FROM Reserva WHERE id = @id";
                SqlCommand com = new SqlCommand(query, connection);
                com.Parameters.AddWithValue("@id", reserva.id);
                // Se obtiene cursor
                dr = com.ExecuteReader();

                // Se lee el primer elemento
                if (dr.Read())
                {
                    reserva.id = Convert.ToInt32(dr["id"].ToString());
                    reserva.fecha = DateTime.Parse(dr["fecha"].ToString());
                    reserva.fechaEntrega = DateTime.Parse(dr["fechaEntrega"].ToString());
                    reserva.pagado = Convert.ToDouble(dr["pagado"].ToString());

                    // Lectura del usuario
                    ENUsuario u = new ENUsuario();
                    u.id = Convert.ToInt32(dr["usuarioID"].ToString());
                    u.readUsuario();
                    reserva.usuario = u;

                    // Lectura del videojuego
                    ENVideojuego v = new ENVideojuego();
                    v.Id = Convert.ToInt32(dr["videojuegoID"].ToString());
                    v.readVideojuego();
                    reserva.videojuego = v;

                    leido = true;
                }
            }
            catch (SqlException sqlex)
            {
                leido = false;
                Console.WriteLine("Reading reserva operation has failed. Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                leido = false;
                Console.WriteLine("Reading reserva operation has failed. Error: {0}", ex.Message);
            }
            finally
            {
                if (dr != null) dr.Close();
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return leido;
        }

        public DataTable mostrarReservas(ENReserva reserva)
        {
            DataTable dataTable = new DataTable();
            try
            {
                c.Open();
                string query = "SELECT r.* , v.titulo as videojuego, u.nombre as usuario" +
                    " FROM Reserva r , videojuego v , usuario u " +
                    "where v.Id = r.videojuegoID and r.usuarioID = u.id;";
                SqlDataAdapter adapter = new SqlDataAdapter(query, c);
                adapter.Fill(dataTable);
            }
            catch (SqlException ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
            }
            finally
            {
                c.Close();
            }
            return dataTable;
        }
    }
}
