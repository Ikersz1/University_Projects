<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="CategoriaWebForm.aspx.cs" Inherits="web.CategoriaWebForm" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 599px;">
        <div class="showGrid">
            <center>
            <div class="izq">
            <h2>Categorias</h2>
            <asp:GridView ID="categoriasTable" runat="server" CssClass="text-primary" AutoGenerateColumns="False" BorderStyle="None" BackColor="White" BorderWidth="2px"
                DataKeyNames="id" PageSize="5" AllowPaging="True" CellPadding="5"
                EmptyDataText="Ups, no se han encontrado categorias."
                OnPageIndexChanging="changePageCategoriasTable"
                OnRowEditing="clickRowEditCategorias"
                OnRowCancelingEdit="clickRowCancelCategorias"
                OnRowUpdating="clickRowUpdateCategorias"
                OnRowDeleting="clickRowDeleteCategorias"
                GridLines="Horizontal">

                <Columns>
                    <asp:BoundField DataField="id" HeaderText="ID" ReadOnly="True" SortExpression="Id" />
                    <asp:BoundField DataField="nombre" HeaderText="Nombre" SortExpression="Nombre" />
                    <asp:BoundField DataField="descripcion" HeaderText="Descripcion" SortExpression="Descripcion" />


                    <asp:CommandField HeaderText="Acciones" ButtonType="Image" CancelImageUrl="~/assets/imagenes/iconos/cancel.png" EditImageUrl="~/assets/imagenes/iconos/edit.png"
                        ShowEditButton="True" ShowDeleteButton="True" ShowCancelButton="True" UpdateImageUrl="~/assets/imagenes/iconos/check.png"
                        DeleteImageUrl="~/assets/imagenes/iconos/trash.png">
                        <ControlStyle Height="20px" Width="20px" CssClass="botones" />
                    </asp:CommandField>

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

            </div>
            <asp:Label ID="Resultado" runat="server" Text =" " />&nbsp;    
            <br />

            <h2>Crear Categoria</h2>  
            <div class="crearFlex">
                 <div class="izq">              
                    Nombre: 
                     <asp:TextBox ID="Nombre" runat="server"></asp:TextBox> 
                     Descripción: 
                    <asp:TextBox ID="Descripcion" runat="server"></asp:TextBox>
                    <asp:Button id="crearCategoria" Text="Crear" runat="server" OnClick="crearCategoriaClick"/>
                 </div>
                 
                

            </div>
        </center>
        </div>
     </div>
</asp:Content>


