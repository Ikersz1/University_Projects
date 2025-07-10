<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="AdminOfertas.aspx.cs" Inherits="web.AdminOfertas" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <div class="row">
            <div class="col-md-2">
                <h2 class="text-primary">Ofertas</h2>
            </div>
            <div class="col-md-7 m-md-3">
                <!--Filtros-->
                <div class="row">


                    <div class="col">
                        <!--Barar busqueda por nombre-->
                        <asp:TextBox ID="filtroNombre" runat="server" placeholder="Oferta..." value="" CssClass="mb-2" />
                        <asp:RegularExpressionValidator ID="regexNombre" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="filtroNombre" ValidationExpression="^[a-zA-Z0-9]*$"></asp:RegularExpressionValidator>
                        <!--Descuento-->
                        Descuento:
                    <asp:TextBox ID="filtroDescuento" runat="server" type="number" min="0" max="100" value="0" CssClass="mb-2"></asp:TextBox>
                        <asp:RangeValidator ID="validatorFiltroDescuento" runat="server" ControlToValidate="filtroDescuento" ErrorMessage="Descuento entre 0 y 100" MaximumValue="100"  MinimumValue="0" Type="Integer"></asp:RangeValidator>

                    </div>
                    <div class="col">
                        <!--DropDown productora-->
                        <asp:DropDownList ID="filtroProductora" AutoPostBack="true" OnSelectedIndexChanged="filtroProductoraOnChange" runat="server" CssClass="mb-2">
                            <Items>
                                <asp:ListItem Text="Productora" Value="0" />
                            </Items>
                        </asp:DropDownList>
                        <!--DropDown videojuego-->
                        <asp:DropDownList ID="filtroVideojuego" AutoPostBack="false" runat="server" CssClass="mb-2">
                            <Items>
                                <asp:ListItem Text="Videojuego" Value="0" />
                            </Items>
                        </asp:DropDownList>
                    </div>
                    <div class="col">
                        Fecha inicio
                        <asp:TextBox ID="fechaInicio" runat="server" type="date" CssClass="mb-2" value=""></asp:TextBox>
                        Fecha fin
                        <asp:TextBox ID="fechaFin" runat="server" type="date" CssClass="mb-2" value=""></asp:TextBox>
                    </div>
                    <div class="col">

                        <!--Boton busqueda-->
                        <asp:Button CssClass="btn btn-primary" ID="filtros" Text="Filtrar" runat="server" OnClick="filtrarOfertasOnClick" />
                        <asp:Button CssClass="btn btn-danger" ID="resetFiltros" Text="Eliminar filtros" runat="server" OnClick="resetFiltrosOnClick" />

                    </div>

                </div>
            </div>
        </div>
        <div class="table-responsive mt-2">
            <asp:GridView ID="ofertasTable" runat="server" AutoGenerateColumns="False" BorderStyle="None" BackColor="White" BorderWidth="2px"
                DataKeyNames="id" PageSize="5" AllowPaging="True" AllowSorting="true" OnSorting="OfertasTable_Sorting" CellPadding="5"
                EmptyDataText="Ups, no se han encontrado ofertas."
                OnPageIndexChanging="changePageOfertasTable"
                OnRowEditing="clickRowEditOferta"
                OnRowCancelingEdit="clickRowCancelOferta"
                OnRowUpdating="clickRowUpdateOferta"
                OnRowDeleting="clickRowDeleteOferta">

                <Columns>
                    <asp:BoundField DataField="id" HeaderText="ID" ReadOnly="True" SortExpression="id" ItemStyle-ForeColor="#0d6efd" />
                    <asp:BoundField DataField="nombre" HeaderText="Nombre" SortExpression="nombre" />
                    <asp:BoundField DataField="videojuego" HeaderText="Videojuego" SortExpression="videojuego" />
                    <asp:BoundField DataField="productora" HeaderText="Productora" SortExpression="productora" />
                    <asp:BoundField DataField="descuento" HeaderText="Descuento" SortExpression="descuento" />
                    <asp:BoundField DataField="fecha_inicio" HeaderText="Fecha Inicio" SortExpression="fecha_inicio" />
                    <asp:BoundField DataField="fecha_fin" HeaderText="Fecha Fin" SortExpression="fecha_fin" />
                    <asp:CommandField HeaderText="Acciones" ButtonType="Image" CancelImageUrl="~/assets/imagenes/iconos/cancel.png" EditImageUrl="~/assets/imagenes/iconos/edit.png"
                        ShowEditButton="True" ShowDeleteButton="True" ShowCancelButton="True" UpdateImageUrl="~/assets/imagenes/iconos/check.png"
                        DeleteImageUrl="~/assets/imagenes/iconos/trash.png">
                        <ControlStyle Height="20px" Width="20px" />
                    </asp:CommandField>

                </Columns>
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
                <h2>Crear oferta</h2>
            </div>
            <div class="col-md-4">
                <asp:Label ID="msgSalidaCrear" runat="server" CssClass="text-white" Text=""></asp:Label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div>
                    Nombre:
                    <asp:TextBox ID="nuevoNombre" runat="server"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorNombre" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevoNombre"></asp:RequiredFieldValidator>
                     <asp:RegularExpressionValidator ID="regexNuevoNombre" runat="server" ForeColor="Red"  ErrorMessage="*"  ControlToValidate="nuevoNombre" ValidationExpression="^[a-zA-Z0-9]*$"></asp:RegularExpressionValidator>
                </div>
                <div class="mt-2">
                    Fecha Inicio:
                    <asp:TextBox ID="nuevaFechaInicio" runat="server" type="date"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorFechaInicio" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevaFechaInicio"></asp:RequiredFieldValidator>
                </div>
                <div class="mt-2">
                    Fecha Fin:
                    <asp:TextBox ID="nuevaFechaFin" runat="server" type="date"></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorFechaFin" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevaFechaFin"></asp:RequiredFieldValidator>
                </div>
            </div>
            <div class="col-md-6">
                <div>
                    Descuento (0-100):
                    <asp:TextBox ID="nuevoDescuento" runat="server" type="number" min="1" max="100" value=""></asp:TextBox>
                    <asp:RequiredFieldValidator ValidationGroup="CreateValidation" ID="validatorNuevoDescuento" runat="server" ForeColor="Red"  ErrorMessage="*" ControlToValidate="nuevoDescuento"></asp:RequiredFieldValidator>
                    <asp:RangeValidator ID="nuevoDescuentoValidator" runat="server" ControlToValidate="nuevoDescuento" ErrorMessage="Descuento entre 0 y 100" MaximumValue="100"  MinimumValue="0" Type="Integer"></asp:RangeValidator>
                </div>
                <div class="mt-2">
                    Productora:
            <asp:DropDownList ID="productorasList" AutoPostBack="true" OnSelectedIndexChanged="ProductoraSelectionChange" runat="server">
                <Items>
                    <asp:ListItem Text="Productora" Value="0" />
                </Items>
            </asp:DropDownList>
                </div>
                <div class="mt-2">
                    Videojuego:
                    <asp:DropDownList ID="videojuegosList" AutoPostBack="false" runat="server">
                        <Items>
                            <asp:ListItem Text="Videojuego" Value="0" />
                        </Items>
                    </asp:DropDownList>
                </div>
            </div>
            <div class="mt-2">
                <asp:Button CssClass="btn btn-primary" ID="crearOferta" Text="Crear" runat="server" OnClick="crearOfertaClick" ValidationGroup="CreateValidation"/>
                <asp:Label ID="msgValidar" CssClass="text-white bg-danger" runat="server"></asp:Label>
            </div>

        </div>
    </div>

</asp:Content>
