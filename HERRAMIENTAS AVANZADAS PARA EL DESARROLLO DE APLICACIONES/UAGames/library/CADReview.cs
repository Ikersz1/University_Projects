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
    public class CADReview
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADReview()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = new SqlConnection(conexionBBDD);
        }

        public bool createReview(ENReview review)
        {
            bool result = true;
            try
            {
                c.Open();
                string query = "INSERT INTO Review (puntuacion, comentario, fecha, usuarioID, videojuegoID) VALUES (@puntuacion, @comentario, @fecha, @usuarioID, @videojuegoID);";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@puntuacion", review.puntuacion);
                com.Parameters.AddWithValue("@comentario", review.comentario);
                com.Parameters.AddWithValue("@fecha", review.fecha.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@usuarioID", review.usuario.id);
                com.Parameters.AddWithValue("@videojuegoID", review.videojuego.Id);
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

        public bool deleteReview(ENReview review)
        {
            bool result = true;

            //Borramos la review
            if (result)
            {
                try
                {
                    c.Open();
                    string query = "DELETE FROM Review WHERE id = @id;";
                    SqlCommand com2 = new SqlCommand(query, c);
                    com2.Parameters.AddWithValue("@id", review.id);
                    com2.ExecuteNonQuery();
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
            }
            return result;
        }

        public DataTable filtrarReview(ENReview review , string ordenColumna)
        {
            DataTable dataTable = new DataTable();
            try
            {
                c.Open();
                string query = "SELECT r.* , v.Titulo as nombrejuego, u.nombre as usuario" +
                    " FROM Review r , videojuego v , usuario u " +
                    "where v.Id = r.videojuegoID and r.usuarioID = u.id and r.videojuegoID = @id ";
               
                switch (ordenColumna)
                {
                    //Columna de puntuacion
                    case "puntuacion":
                        query += "order by r." + ordenColumna + " desc;";
                        break;
                    //Por defecto ordena por fecha descendente
                    default:
                        query += "order by r.fecha desc;";
                        break;
                }

                SqlCommand com1 = new SqlCommand(query, c);
                com1.Parameters.AddWithValue("@id", review.videojuego.Id);
                SqlDataAdapter adapter = new SqlDataAdapter(com1);
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

        public bool updateReview(ENReview review)
        {
            bool result = true;

            try
            {
                c.Open();
                string query = "UPDATE Review SET puntuacion = @puntuacion, comentario = @comentario, fecha = @fecha , videojuegoID = @videojuegoID WHERE id = @id;";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@puntuacion", review.puntuacion);
                com.Parameters.AddWithValue("@comentario", review.comentario);
                com.Parameters.AddWithValue("@fecha", review.fecha.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@id", review.id);
                com.Parameters.AddWithValue("@videojuegoID", review.videojuego.Id);
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

        public bool readReview(ENReview review)
        {
            bool leido = false;
            SqlDataReader dr = null;
            try
            {
                c.Open();
                string query = "SELECT r.* , u.nick as nick FROM Review r , Usuario u WHERE r.usuarioID = u.id and r.id = @id";
                SqlCommand com = new SqlCommand(query, c);
                com.Parameters.AddWithValue("@id", review.id);
                //Se obtiene cursor
                dr = com.ExecuteReader();

                //Se lee primer elemento
                if (dr.Read())
                {
                    review.id = Convert.ToInt32(dr["id"].ToString());
                    review.fecha = DateTime.Parse(dr["fecha"].ToString());
                    review.comentario = dr["comentario"].ToString();
                    review.puntuacion = Convert.ToInt32(dr["puntuacion"].ToString());

                    //Lectura del videojuego
                    ENVideojuego v = new ENVideojuego();
                    v.Id = Convert.ToInt32(dr["videojuegoID"].ToString());
                    v.readVideojuegoId();

                    //Lectura del usuario
                    ENUsuario u = new ENUsuario();
                    u.nick = dr["nick"].ToString();
                    u.readUsuario();

                    review.videojuego = v;
                    review.usuario = u;

                    leido = true;
                }

            }
            catch (SqlException sqlex)
            {
                leido = false;
                Console.WriteLine("Reading review operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                leido = false;
                Console.WriteLine("Reading review operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (dr != null) dr.Close();
                if (c != null) c.Close(); // Se asegura de cerrar la conexión.
            }
            return leido;
        }

        //Funcion hecha por si en un futuro se quiere añadir un boton en el menú de mis reseñas
        public DataTable misReviews(ENReview review)
        {
            DataTable dataTable = new DataTable();
            try
            {
                c.Open();
                string query = "SELECT r.* , v.Imagen as imagen , v.Titulo as nombrejuego, u.nombre as usuario" +
                    " FROM Review r , videojuego v , usuario u " +
                    "where v.Id = r.videojuegoID and r.usuarioID = u.id and r.usuarioID = @usuarioID;";
                SqlCommand com1 = new SqlCommand(query, c);
                com1.Parameters.AddWithValue("@usuarioID", review.usuario.id);
                SqlDataAdapter adapter = new SqlDataAdapter(com1);
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

        public DataTable mostrarReview(ENReview review)
        {
            DataTable dataTable = new DataTable();
            try
            {
                c.Open();
                string query = "SELECT r.* , v.Imagen as imagen , v.Titulo as nombrejuego, u.nombre as usuario" +
                    " FROM Review r , videojuego v , usuario u " +
                    "where v.Id = r.videojuegoID and r.usuarioID = u.id and r.id = @id;";
                SqlCommand com1 = new SqlCommand(query, c);
                com1.Parameters.AddWithValue("@id", review.id);
                SqlDataAdapter adapter = new SqlDataAdapter(com1);
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
