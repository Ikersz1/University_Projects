<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="AdminVideojuegos.aspx.cs" Inherits="web.AdminVideojuegos" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 599px;">
        <div class="row">
            <div class="col-md-2">
                <h2 class="text-primary">Videojuegos</h2>
            </div>
            <div class="col-md-7 m-md-3">
                <!--Filtros-->
                <div class="row">


                    <div class="col">
                        <!--Barar busqueda por nombre-->
                        <asp:TextBox ID="filtroTitulo" runat="server" placeholder="Videojuego..." value="" CssClass="mb-2" />
                        <asp:RegularExpressionValidator ID="regexFiltroTitulo" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="filtroTitulo" ValidationExpression="^[a-zA-Z0-9]*$"></asp:RegularExpressionValidator>
                        <!--Precio-->
                        Precio:
                    <asp:TextBox ID="filtroPrecio" runat="server" type="number" min="0" value="0" CssClass="mb-2"></asp:TextBox>
                         <asp:RegularExpressionValidator ID="regexFiltroPrecio" runat="server" ForeColor="Red"  ErrorMessage="El numero tiene que estar separado por comas"  ControlToValidate="filtroPrecio" ValidationExpression="^[0-9]+(?:,[0-9]+)?$"></asp:RegularExpressionValidator>

                    </div>
                    <div class="col">
                        <!--DropDown productora-->
                        <asp:DropDownList ID="filtroProductora" AutoPostBack="false" runat="server" CssClass="mb-2">
                            <Items>
                                <asp:ListItem Text="Productora" Value="0" />
                            </Items>
                        </asp:DropDownList>
                        <!--DropDown categoria-->
                        <asp:DropDownList ID="filtroCategoria" AutoPostBack="false" runat="server" CssClass="mb-2">
                            <Items>
                                <asp:ListItem Text="Categoria" Value="0" />
                            </Items>
                        </asp:DropDownList>
                    </div>
                    <div class="col">
                        <!--Plataforma-->
                        <asp:TextBox ID="filtroPlataforma" runat="server" PlaceHolder="Plataforma..." value="" CssClass="mb-2"></asp:TextBox>
                        <!--Fecha lanzamiento-->
                        Fecha lanzamiento:
                        <asp:TextBox ID="filtroFecha" runat="server" type="date" CssClass="mb-2" value=""></asp:TextBox>
                    </div>
                    <div class="col">

                        <!--Boton busqueda-->
                        <asp:Button CssClass="btn btn-primary" ID="filtros" Text="Filtrar" runat="server" OnClick="filtrarOnClick" />
                        <asp:Button CssClass="btn btn-danger mt-3" ID="resetFiltros" Text="Eliminar filtros" runat="server" OnClick="resetFiltrosOnClick" />

                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <asp:GridView ID="videojuegoTable" runat="server" CssClass="text-primary" AutoGenerateColumns="False" BorderStyle="None" BackColor="White" BorderWidth="2px"
                DataKeyNames="id" PageSize="5" AllowPaging="True" AllowSorting="true" OnSorting="VideojuegosTable_Sorting" CellPadding="5"
                EmptyDataText="Ups, no se han encontrado videojuegos."
                OnPageIndexChanging="changePageVideojuegosTable"
                OnRowEditing="clickRowEditVideojuego"
                OnRowCancelingEdit="clickRowCancelVideojuego"
                OnRowUpdating="clickRowUpdateVideojuego"
                OnRowDeleting="clickRowDeleteVideojuego"
                GridLines="Horizontal">

                <Columns>
                    <asp:BoundField DataField="id" HeaderText="ID" ReadOnly="True" SortExpression="id" />
                    <asp:BoundField DataField="titulo" HeaderText="Titulo" SortExpression="titulo" />
                    <asp:BoundField DataField="productora" HeaderText="Productora" SortExpression="productora" />
                    <asp:BoundField DataField="categoria" HeaderText="Categoria" SortExpression="categoria" />
                    <asp:BoundField DataField="fecha_lanzamiento" HeaderText="Fecha Lanzamiento" SortExpression="fecha_lanzamiento" />
                    <asp:BoundField DataField="precio" HeaderText="Precio" SortExpression="precio" />
                    <asp:BoundField DataField="plataforma" HeaderText="Plataforma" SortExpression="plataforma" />
                    <asp:ImageField DataImageUrlField="imagen" HeaderText="Imagen" NullDisplayText="X" ControlStyle-Width="50px">
                    </asp:ImageField>
                    <asp:BoundField DataField="descripcion" HeaderText="Descripcion" SortExpression="Descripcion" />
                    <asp:CommandField HeaderText="Acciones" ButtonType="Image" CancelImageUrl="~/assets/imagenes/iconos/cancel.png" EditImageUrl="~/assets/imagenes/iconos/edit.png"
                        ShowEditButton="True" ShowDeleteButton="True" ShowCancelButton="True" UpdateImageUrl="~/assets/imagenes/iconos/check.png"
                        DeleteImageUrl="~/assets/imagenes/iconos/trash.png">
                        <ControlStyle Height="20px" Width="20px" />
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
        <br />
        <div class="row">
            <div class="col-md-6">
                <h2>Crear videojuego</h2>
            </div>
            <div class="col-md-4">
                <asp:Label ID="msgSalidaCrear" CssClass="text-white" runat="server" Text=""></asp:Label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div>
                    Titulo:
            <asp:TextBox ID="nuevoTitulo" runat="server"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorTitulo" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevoTitulo"></asp:RequiredFieldValidator>
                     <asp:RegularExpressionValidator ID="regexTitulo" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="nuevoTitulo" ValidationExpression="^[a-zA-Z0-9]*$"></asp:RegularExpressionValidator>
                </div>
                <div class="mt-2">
                    Fecha lanzamiento:
            <asp:TextBox ID="nuevaFechaLanzamiento" runat="server" type="date"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorFecha" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevaFechaLanzamiento"></asp:RequiredFieldValidator>
                </div>
                <div class="mt-2">
                    Precio:
                    <asp:TextBox ID="nuevoPrecio" runat="server"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorPrecio" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevoPrecio"></asp:RequiredFieldValidator>
                    <asp:RegularExpressionValidator ID="regexPrecio" runat="server" ForeColor="Red"  ErrorMessage="El numero tiene que estar separado por comas"  ControlToValidate="nuevoPrecio" ValidationExpression="^[0-9]+(?:,[0-9]+)?$"></asp:RegularExpressionValidator>
                </div>

            </div>
            <div class="col-md-4">
                <div class="mt-2">
                    Plataforma:
                    <asp:TextBox ID="nuevaPlataforma" runat="server"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorPlataforma" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevaPlataforma"></asp:RequiredFieldValidator>
                    <asp:RegularExpressionValidator ID="regexPlataforma" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="nuevaPlataforma" ValidationExpression="^[a-zA-Z0-9]*$"></asp:RegularExpressionValidator>
                </div>
                <div class="mt-2">
                    Descripcion:
                    <asp:TextBox ID="nuevaDescripcion" runat="server"></asp:TextBox>
                     <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorDescripcion" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevaDescripcion"></asp:RequiredFieldValidator>
                    <asp:RegularExpressionValidator ID="regexDescripcion" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="nuevaDescripcion" ValidationExpression="^[a-zA-Z0-9\s\.,_-]*$"></asp:RegularExpressionValidator>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mt-2">
                    Productora:
            <asp:DropDownList ID="productorasList" AutoPostBack="false" runat="server">
                <Items>
                    <asp:ListItem Text="Productora" Value="0" />
                </Items>
            </asp:DropDownList>
                </div>
                <div class="mt-2">
                    Categoria:
                    <asp:DropDownList ID="categoriasList" AutoPostBack="false" runat="server">
                        <Items>
                            <asp:ListItem Text="Categoria" Value="0" />
                        </Items>
                    </asp:DropDownList>
                </div>
            </div>
            <div class="mt-2">
                <asp:Button ID="crearVideojuego" CssClass="btn btn-primary" Text="Crear" runat="server" OnClick="crearVideojuegoClick" ValidationGroup="CreateValidation"/>
                <asp:Label ID="msgValidar" CssClass="text-white bg-danger" runat="server"></asp:Label>
            </div>
        </div>
    </div>
</asp:Content>
