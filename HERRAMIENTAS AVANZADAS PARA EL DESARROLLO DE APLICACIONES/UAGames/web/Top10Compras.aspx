<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Compras.aspx.cs" Inherits="web.Compras" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <div class="row">
                <h2 class="text-primary">Top 10 clientes</h2>
            </div>
            <div class="row mt-5">
                <div class="col-md-4">
                    <asp:GridView ID="GridView1" runat="server" CssClass="grid" AutoGenerateColumns="False" BackColor="White" BorderColor="#999999" BorderStyle="None" BorderWidth="1px" CellPadding="3" DataSourceID="SqlDataSource" GridLines="Vertical">
                <AlternatingRowStyle BackColor="#DCDCDC" />
                <Columns>
                    <asp:BoundField DataField="Usuario" HeaderText="Usuario" SortExpression="Usuario" />
                    <asp:BoundField DataField="Numero de Compras" HeaderText="Numero de Compras" SortExpression="Numero de Compras" ReadOnly="True" />
                </Columns>
                <FooterStyle BackColor="White" ForeColor="#0d6efd" />
                <HeaderStyle BackColor="#0d6efd" Font-Bold="True" ForeColor="White" HorizontalAlign="Center" Font-Underline="false" />
                <PagerStyle BackColor="White" ForeColor="#0d6efd" HorizontalAlign="Center" />
                <RowStyle ForeColor="#0d6efd" />
                <SelectedRowStyle BackColor="#669999" Font-Bold="True" ForeColor="White" />
                <SortedAscendingCellStyle BackColor="#F1F1F1" />
                <SortedAscendingHeaderStyle BackColor="#007DBB" />
                <SortedDescendingCellStyle BackColor="#CAC9C9" />
                <SortedDescendingHeaderStyle BackColor="#00547E" />
            </asp:GridView>
            <asp:SqlDataSource ID="SqlDataSource" runat="server" ConnectionString="<%$ ConnectionStrings:miconexion %>" SelectCommand="SELECT TOP 10 Usuario.nick AS 'Usuario', COUNT(*) AS 'Numero de Compras' 
FROM Compra INNER JOIN Usuario ON Compra.usuarioID = Usuario.id 
GROUP BY Usuario.nick 
ORDER BY COUNT(*) DESC
"></asp:SqlDataSource>
            <asp:SqlDataSource ID="UAGAMES" runat="server" ConnectionString="<%$ ConnectionStrings:miconexion %>" SelectCommand="SELECT TOP 10 Usuario.nick AS 'Usuario', COUNT(*) AS 'Numero de Compras' FROM Compra INNER JOIN Usuario ON Compra.usuarioID = Usuario.id GROUP BY Usuario.nick ORDER BY COUNT(*) DESC"></asp:SqlDataSource>
                </div>
                <div class="col-md-8">
                    <canvas id="barChart" runat="server" width="500" height="500" ></canvas>
            <script>
                document.addEventListener("DOMContentLoaded", function () {

                    // Obtener el elemento canvas del gráfico
                    var barChart = document.getElementById("main_barChart").getContext('2d');

                    // Configurar los datos para el gráfico
                    var data = {
                        labels: ["Omar", "Clara", "Estevan", "Adrian", "Aitor", "Raul", "Iker", "Paco","Jose", "Pepe"], // Obtén los datos de Usuario desde la fuente de datos
                        datasets: [{
                            label: 'Número de Compras',
                            data: [3, 5, 4, 6, 4, 4, 2, 1, 3, 3], // Obtén los datos de "Numero de Compras" desde la fuente de datos
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    };

                    // Configurar las opciones del gráfico
                    var options = {
                        responsive: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    };

                    // Crear el gráfico de barras
                    var chart = new Chart(barChart, {
                        type: 'bar',
                        data: data,
                        options: options
                    });
                });
            </script>

                </div>

            </div>
            
           
    </div>

</asp:Content>



