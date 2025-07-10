<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Inicio.aspx.cs" Inherits="web.Inicio" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <h1 class="text-primary">UA Games </h1>

        <div id="carouselExampleIndicators" class="carousel slide text-primary bg-dark text-center m-auto" data-ride="carousel" style="max-height: 500px; max-width: 1000px;">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner" style="max-height: 500px; max-width: 1000px;">
                <div class="carousel-item active">
                    <asp:HyperLink ID="linkslider1" runat="server" NavigateUrl='Catalogo.aspx'>
                        <asp:Image ID="slider1" runat="server" ImageUrl='assets/imagenes/videojuegos/slider1.jpg' CssClass="d-block w-100" Style="object-fit: cover; overflow: hidden; object-position: center;" alt="First slide" />
                    </asp:HyperLink>
                </div>
                <div class="carousel-item">
                    <asp:HyperLink ID="linkSlider2" runat="server" NavigateUrl='Catalogo.aspx'>
                        <asp:Image ID="slider2" runat="server" ImageUrl='assets/imagenes/videojuegos/slider2.jpg' CssClass="d-block w-100" Style="object-fit: cover; overflow: hidden; object-position: center;" alt="First slide" />
                    </asp:HyperLink>
                </div>
                <div class="carousel-item">
                    <asp:HyperLink ID="linkSlider3" runat="server" NavigateUrl='Catalogo.aspx'>
                        <asp:Image ID="slider3" runat="server" ImageUrl='assets/imagenes/videojuegos/slider3.jpg' CssClass="d-block w-100" Style="object-fit: cover; overflow: hidden; object-position: center;" alt="First slide" />
                    </asp:HyperLink>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next color-primary" style="font-size: 15px;" href="#carouselExampleIndicators" role="button" data-slide="next" style="color: #0d6efd">
                    <span class="carousel-control-next-icon color-primary" aria-hidden="true"></span>
                    <span class="sr-only color-primary">Next</span>
                </a>
            </div>
        </div>

        <br />
        <h3>Ofertas: </h3>
        <asp:ListView ID="ofertasListView" runat="server" GroupItemCount="3" >
            <LayoutTemplate>
                <div>
                    <table runat="server">
                        <tr runat="server" id="groupPlaceholder">
                        </tr>
                    </table>
                    <div class="text-center">
                        <asp:DataPager runat="server" ID="DataPager" PageSize="8"  HorizontalAlign="Center" CssClass="text-center text-primary">
                        <Fields>
                            <asp:NextPreviousPagerField ButtonType="Button" ShowFirstPageButton="false" ShowLastPageButton="False" ShowNextPageButton="False" ShowPreviousPageButton="True"
                                PreviousPageText=" < " FirstPageText=" << " />
                            <asp:NumericPagerField />
                            <asp:NextPreviousPagerField ButtonType="Button" ShowFirstPageButton="False" ShowLastPageButton="false" ShowNextPageButton="True" ShowPreviousPageButton="False"
                                NextPageText=" > " LastPageText=" >> " />
                        </Fields>
                    </asp:DataPager>
                    </div>
                    
                </div>

            </LayoutTemplate>
            <GroupTemplate>
                <tr runat="server" id="ofertarow" class="mx-4">
                    <td runat="server" id="itemPlaceholder"></td>
                </tr>
            </GroupTemplate>
            <ItemTemplate>
                <td class="mx-4">

                    <div class="card bg-primary text-white mx-3 my-2">
                        <div class="row">
                            <div class="col-4">
                                <asp:HyperLink ID="link" runat="server" NavigateUrl='<%#$"Videojuego.aspx?id={Eval("vid")}" %>'>
                                    <asp:Image ID="imagenCard" runat="server" ImageUrl='<%# Eval("imagen") %>' CssClass="img-fluid" style="height: 10rem; object-fit: cover;" />
                                </asp:HyperLink>
                            </div>
                            <div class="col-8 mt-3">
                                <div class="card-block px-2">
                                    <h6>
                                        <asp:HyperLink ID="linkOferta" CssClass="text-white card-title" runat="server" Text='<%# Eval("nombre") %>' NavigateUrl='<%#$"Videojuego.aspx?id={Eval("vid")}" %>'/></h6>
                                    <h6><%# Eval("productora") %></h6>
                                    <h6>Descuento: <%# Eval("descuento") %>%</h6>
                                    <h6>Hasta: <%# Eval("fecha_fin") %></h6>
                                </div>
                            </div>
                        </div>

                    </div>
                </td>
            </ItemTemplate>
            <EmptyDataTemplate>
                <table runat="server" style="">
                    <tr>
                        <td>Ups, no se han encontrado ofertas.</td>
                    </tr>
                </table>
            </EmptyDataTemplate>
        </asp:ListView>

    </div>
</asp:Content>


