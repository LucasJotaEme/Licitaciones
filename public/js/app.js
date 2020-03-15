$( document ).ready(function() {
 
    var notificaciones = $('#notificacion');
    
    notificaciones.slideToggle(5000);
    
    
    
    $('#formConEfecto').hide();
    $('#formConEfecto').slideDown(500);
    
    
    $('#idConexion').hide();
    $('#idConexion').slideToggle(250);
    $('.ultimaConexion').click(function() {
        $('#idConexion').slideToggle('slow');
    });
    
    $('#sistema').hover(function() {
        var tamano = $(".sistemaImagen").width();
        
        if (tamano===200){
                tamano = 150;
        }else{
                tamano = 200;
        }
        $(".sistemaImagen").animate({
            width: tamano
        });
        
    });
    
    $('#show_password').on('change',function(event){
        // Si el checkbox esta "checkeado"
        if($('#show_password').is(':checked')){
           // Convertimos el input de contraseña a texto.
           $('#inputPassword').get(0).type='text';
        // En caso contrario..
        } else {
           // Lo convertimos a contraseña.
           $('#inputPassword').get(0).type='password';
        }
    });
    
    
    $(".loginDivFormulario").animate({
      width: '+=350px'
    });
    
     $('.search-slt').keyDown(function(){
        $('.search-slt').data();
    });
});
