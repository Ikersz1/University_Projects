<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Catalogo.aspx.cs" Inherits="web.Catalogo" %>

<%@ Register Assembly="AjaxControlToolkit" Namespace="AjaxControlToolkit" TagPrefix="ajaxToolkit" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <div class="row">
            <div class="col-md-3 align-middle">
                <h1 class="text-center text-primary">Videojuegos</h1>
                <asp:DropDownList ID="ordenar" AutoPostBack="false" runat="server" CssClass="mb-2 mx-5 px-5 mt-4">
                            <Items>
                                <asp:ListItem Text="Ordenar por:" Value="id" />
                                <asp:ListItem Text="Titulo" Value="titulo" />
                                <asp:ListItem Text="Categoria" Value="categoria" />
                                <asp:ListItem Text="Productora" Value="productora" />
                                <asp:ListItem Text="Precio" Value="precio" />
                                <asp:ListItem Text="Plataforma" Value="plataforma" />
                            </Items>
            </asp:DropDownList>
            </div>
            <div class="col-md-8 m-md-3">
                <!--Filtros-->
                <div class="row">
                    <div class="col">
                        <!--Barar busqueda por nombre-->
                        <asp:TextBox ID="filtroTitulo" runat="server" placeholder="Videojuego..." value="" CssClass="mb-2" />
                        <!--Precio-->
                        Precio:
                        <asp:TextBox ID="filtroPrecio" runat="server" type="number" min="0" value="100" CssClass="mb-2"></asp:TextBox>
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



        <asp:ListView ID="VideojuegosListView" runat="server" GroupItemCount="4">
            <LayoutTemplate>
                <div>
                    <table runat="server">
                        <tr runat="server" id="groupPlaceholder">
                        </tr>
                    </table>
                    <div class="text-center">
                    <asp:DataPager runat="server" ID="DataPager" PageSize="8"  HorizontalAlign="Center" CssClass="text-center text-primary">
                        <Fields>
                            <asp:NextPreviousPagerField ButtonType="Button" ShowFirstPageButton="True" ShowLastPageButton="False" ShowNextPageButton="False" ShowPreviousPageButton="True" ButtonCssClass="btn btn-primary"
                                PreviousPageText=" < " FirstPageText=" << " />
                            <asp:NumericPagerField />
                            <asp:NextPreviousPagerField ButtonType="Button" ShowFirstPageButton="False" ShowLastPageButton="True" ShowNextPageButton="True" ShowPreviousPageButton="False" ButtonCssClass="btn btn-primary"
                                NextPageText=" > " LastPageText=" >> " />
                        </Fields>
                    </asp:DataPager>
                </div>
                    </div>

            </LayoutTemplate>
            <GroupTemplate>
                <tr runat="server" id="videojuegoRow" class="mx-4">
                    <td runat="server" id="itemPlaceholder"></td>
                </tr>
            </GroupTemplate>
            <ItemTemplate>
                <td class="mx-4">
                    <div class="card bg-primary text-white mx-5 my-3" style="width: 12rem !important;">
                        <asp:HyperLink ID="link" runat="server" NavigateUrl='<%#$"Videojuego.aspx?id={Eval("id")}" %>'>
                            <asp:Image ID="imagenCard" runat="server" ImageUrl='<%# Eval("imagen") %>' CssClass="card-img-top" style="height: 15rem; object-fit: cover;" />
                        </asp:HyperLink>

                        <div class="card-body text-center">
                            <asp:HyperLink ID="linkProductora"  CssClass="text-white" runat="server" Text='<%# Eval("productora") %>' NavigateUrl='<%#$"Productora.aspx?id={Eval("id")}" %>' /> <br />
                            <asp:HyperLink ID="linkCategoria"  CssClass="text-white" runat="server" Text='<%# Eval("categoria") %>' NavigateUrl='<%#$"Catalogo.aspx?categoria={Eval("id")}" %>' /><br />
                            <asp:Label id="plataforma" runat="server" Text='<%# Eval("plataforma") %>'></asp:Label><br />
                            <asp:Label id="precio" runat="server" Text='<%# Eval("nuevoPrecio") %>'></asp:Label> € <br />
                            <div class="text-center justify-content-between justify-content-around d-flex flex-row">
                                <asp:ImageButton ID="addCart" runat="server" AlternateText="Add Cart" ImageAlign="middle" style="height: 3rem;"
                                    ImageUrl="assets/imagenes/iconos/cartAdd.png" OnClientClick="clickAddCart" OnClick="clickAddCart" CommandArgument='<%# Eval("id") %>' />
                                <asp:ImageButton ID="addList" runat="server" AlternateText="Add List" ImageAlign="middle" style="height: 3rem;"
                                    ImageUrl="assets/imagenes/iconos/addList.png" OnClientClick="clickAddList" OnClick="clickAddList" CommandArgument='<%# Eval("id") %>' />
                            </div>
                        </div>
                    </div>
                </td>
            </ItemTemplate>
            <EmptyDataTemplate>
                <table runat="server" style="">
                    <tr>
                        <td>Ups, no se han encontrado videojuegos.</td>
                    </tr>
                </table>
            </EmptyDataTemplate>
        </asp:ListView>

    </div>
</asp:Content>
