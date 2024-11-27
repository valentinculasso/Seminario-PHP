# PHP

<!--

Proyecto seminario de PHP

NUEVOS ENDPOINTS:

get('/plataforma'): Obtiene todas las plataformas. Solo lo puede hacer un usuario logeado y que sea administrador

post('/soporte'): agrega un soporte a la base de datos. Solo lo puede hacer un usuario logeado y que sea administrador

get('calificacionescompletas'): Obtiene todas las calificaciones de todos los usuarios.

get('calificaciones'): Obtiene las calificaciones de un usuario, solo lo puede hacer un usuario logueado

MODIFICACIONES:

metodo getPagina(): se agrego una nueva consulta en la cual ademas de retornar la paginacion de juegos, retorna tambien la cantidad de juegos totales. Esto lo hicimos para poder manejar adecuadamente la paginacion con los botones de anterior y siguiente

metodo agregarJuego(): ademas del result tambien devuelve el id del juego que fue creado. Esto lo hicimos para que al crear un juego se pueda guardar el id nuevo en localstorage y asi poder usarlo para crear los soportes

metodo login(): se modifico el atributo "date" del token ya que antes guardaba la hora en la que fue creado, ahora guarda la hora en la que vence. Esto lo hicimos para que con el token se maneje todo en el front, de esta manera con solo tener el token se accede al id, vencimiento y es_admin del usuario que se logueo.

-->