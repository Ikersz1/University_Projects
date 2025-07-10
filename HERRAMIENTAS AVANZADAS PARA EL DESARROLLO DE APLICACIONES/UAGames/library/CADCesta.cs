using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    class CADCesta
    {
        private string conexionBBDD;
        

        public CADCesta()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
        }
        public bool addVideojuego(ENCesta ces,int videojuegoID)
        {
            bool añadir = false;
            SqlConnection connection = null;

            try
            {
                CADVideojuego videojuego = new CADVideojuego();
                videojuego.readVideojuego(ces.videojuegoID);

                CADUsuario usuario = new CADUsuario();
                usuario.readUsuario(ces.usuarioID);

                connection = new SqlConnection(conexionBBDD);
                connection.Open();

                string selectQuery = "SELECT COUNT(*) FROM [CestaCompra] WHERE usuarioID = @usuarioID AND videojuegoID = @videojuegoID;";
                SqlCommand selectCommand = new SqlCommand(selectQuery, connection);
                selectCommand.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);
                selectCommand.Parameters.AddWithValue("@videojuegoID", videojuegoID);
                int count = (int)selectCommand.ExecuteScalar();

                if (count > 0)
                {
                    // El videojuego ya está en la cesta, incrementar la cantidad
                    string updateQuery = "UPDATE [CestaCompra] SET cantidad = cantidad + @cantidad WHERE usuarioID = @usuarioID AND videojuegoID = @videojuegoID;";
                    SqlCommand updateCommand = new SqlCommand(updateQuery, connection);
                    updateCommand.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);
                    updateCommand.Parameters.AddWithValue("@videojuegoID", videojuegoID);
                    updateCommand.Parameters.AddWithValue("@cantidad", ces.cantidad);
                    updateCommand.ExecuteNonQuery();
                }


                else
                {
                    // El videojuego no está en la cesta, insertarlo
                    string insertQuery = "INSERT INTO [CestaCompra] (usuarioID, videojuegoID, fecha, cantidad) VALUES (@usuarioID, @videojuegoID, @fecha, @cantidad);";
                    SqlCommand insertCommand = new SqlCommand(insertQuery, connection);
                    insertCommand.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);
                    insertCommand.Parameters.AddWithValue("@videojuegoID", videojuegoID);
                    insertCommand.Parameters.AddWithValue("@fecha", ces.fecha.ToString("yyyy/MM/dd"));
                    insertCommand.Parameters.AddWithValue("@cantidad", ces.cantidad);
                    insertCommand.ExecuteNonQuery();
                }


                añadir = true;
            }
            catch (SqlException e)
            {
                añadir = false;
                Console.WriteLine("Creating videogame operation has failed.Error: {0}", e.Message);
            }
            catch (Exception e)
            {
                añadir = false;
                Console.WriteLine("Creating videogame operation has failed.Error: {0}", e.Message);
            }
            finally
            {
                if (connection != null)
                {
                    connection.Close();
                }
            }
            return añadir;

        }

        public bool createCesta(ENCesta ces, ENVideojuego videojuego, ENUsuario usuario) 
        {
            bool crear = false;
            SqlConnection conect = null;
            string query = "INSERT INTO [CestaCompra]" + "(usuarioID,videojuegoID,fecha,cantidad)" + "VALUES (@usuarioID, @videojuegoID,@fecha,@cantidad);";

            try
            {
                conect = new SqlConnection(conexionBBDD);
                conect.Open();
                SqlCommand com = new SqlCommand(query, conect);

                com.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);
                com.Parameters.AddWithValue("@videojuegoID", ces.videojuegoID.Id);
                com.Parameters.AddWithValue("@fecha", ces.fecha);
                com.Parameters.AddWithValue("@cantidad", ces.cantidad);

                com.ExecuteNonQuery();
                crear = true;
            }
            catch (SqlException sqlex)
            {
                
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                crear = false;

            }

            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                crear = false;
            }
            
            finally
            {
                if (conect != null)
                {
                    conect.Close();
                }
            }

            return crear;
        }

        public bool readCesta(ENCesta ces)
        {
            bool controlador = false;
            SqlConnection conect = null;
            string query = "SELECT * FROM [CestaCompra] WHERE usuarioID = @usuarioID";

            SqlDataReader dr = null;

            try
            {
                conect = new SqlConnection(conexionBBDD);
                conect.Open();

                SqlCommand com = new SqlCommand(query, conect);
                com.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);

                dr = com.ExecuteReader();

                if (dr.Read())
                {
                    ENUsuario usu = new ENUsuario();
                    usu.id = Int32.Parse(dr["usuarioID"].ToString());

                    ENVideojuego vid = new ENVideojuego();
                    vid.Id = Int32.Parse(dr["viedeojuegoID"].ToString());

                    ces.fecha = DateTime.Parse(dr["fecha"].ToString());
                    ces.cantidad = Int32.Parse(dr["cantidad"].ToString());


                    controlador = true;
                }
                dr.Close();
            }

            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                controlador = false;
            }

            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                controlador = false;
            }

            finally
            {
                if (conect != null) conect.Close();
            }

            return controlador;
        }

        public DataTable readCestas(ENCesta cesta)
        {   
            SqlConnection conect = null;
            DataTable cestas = new DataTable();

            try
            {
                conect = new SqlConnection(conexionBBDD);
                conect.Open();

                ENUsuario usuaurio = new ENUsuario();


                string query = "SELECT c.usuarioID, c.videojuegoID, v.titulo, c.fecha, c.cantidad ,SUM(v.precio*c.cantidad) as total FROM Usuario u " +
               "INNER JOIN CestaCompra c ON u.id = c.usuarioID " +
               "INNER JOIN Videojuego v ON c.videojuegoID = v.id " +
               "WHERE u.id ='" + cesta.usuarioID.id + "'" +
               "GROUP BY c.usuarioID, c.videojuegoID, v.titulo, c.fecha, c.cantidad;";

                SqlDataAdapter adapter = new SqlDataAdapter(query, conect);
                adapter.Fill(cestas);
            }
            catch (SqlException sqlex)
            {

                Console.WriteLine("Reading ofertas operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {

                Console.WriteLine("Reading ofertas operation has failed.Error: {0}", ex.Message);
            }
            finally
            {

                if (conect != null) conect.Close(); // Se asegura de cerrar la conexión.
            }
            return cestas;
        }

        public bool updateCesta(ENCesta ces, ENUsuario usuario, ENVideojuego videojuego)
        {
            SqlConnection conect = null;

            try
            {
                conect.Open();
                string query = "update CestaCompra set" + "videojuegoID = '" + videojuego.Id + "' where usuarioID = '" + usuario.id + "';";
                SqlCommand com = new SqlCommand(query, conect);

                com.ExecuteReader();

            }

            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }

            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }

            finally
            {
                conect.Close();
            }

            return true;
        }

        public bool deleteCesta(ENCesta ces)
        {
            bool controlador = false;
            SqlConnection conect = null;
            string query = "DELETE FROM CestaCompra WHERE usuarioID = @usuarioID AND videojuegoID = @videojuegoID";

            try
            {
                conect = new SqlConnection(conexionBBDD);
                conect.Open();
                SqlCommand com = new SqlCommand(query, conect);
                com.Parameters.AddWithValue("@usuarioID", ces.usuarioID.id);
                com.Parameters.AddWithValue("@videojuegoID", ces.videojuegoID.Id);
                com.ExecuteReader();
                controlador = true;

            }

            catch (SqlException sqlex)
            {
                controlador = false;
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);

            }

            catch (Exception ex)
            {
                controlador = false;
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
            }

            finally
            {
                conect.Close();
            }

            return controlador;
        }
        public int articulosCesta(ENUsuario usuario)
        {
            int videojuegoID = 0;
            using (SqlConnection conect = new SqlConnection(conexionBBDD))
            {
                conect.Open();
                string query = "SELECT CESTACOMPRA.videojuegoID FROM USUARIO INNER JOIN CESTACOMPRA ON Usuario.id = CESTACOMPRA.usuarioID WHERE Usuario.id = @UsuarioID";
                SqlCommand com = new SqlCommand(query, conect);
                com.Parameters.AddWithValue("@UsuarioID", usuario.id);

                SqlDataReader reader = com.ExecuteReader();

                if (reader.Read())
                {
                    videojuegoID = Convert.ToInt32(reader["videojuegoID"]);
                }

                reader.Close();
                conect.Close();
            }
            return videojuegoID;
        }

    }
}
