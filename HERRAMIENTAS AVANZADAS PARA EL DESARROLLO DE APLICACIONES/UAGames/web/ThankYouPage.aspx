<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="ThankYouPage.aspx.cs" Inherits="web.ThankYouPage" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">

        <div class="container bg-light overflow-auto" style="min-height: 625px;">
            <div class="container d-flex flex-column" style="margin: 0 auto; padding: 50px; background-color: #fff; border-radius: 5px;box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);justify-content: center; align-items: center; max-width: 600px;">
                    <center>
                        <h1 style="color:#333; font-size:30px; margin-bottom:20px;">¡Gracias por tu compra!</h1>
                    <p style="color: #666; font-size: 18px; margin-bottom: 30px;">Tu compra ha sido procesada con éxito.</p>
                    <p style="color: #666; font-size: 18px; margin-bottom: 30px;">Recibirás un correo electrónico con los detalles de tu compra en breve.</p>
                    <a class="btn btn-primary" href="Inicio.aspx">Volver a la página principal</a>
                    </center>
                    
                </div>
        </div>

</asp:Content>
