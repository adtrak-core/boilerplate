function AdminNotice(msg, type) {
    if(!type){ type = "notice-warning" }
    /* create notice div */

    var div = document.createElement( 'div' );
    div.classList.add( 'notice', 'is-dismissible', type );

    /* create paragraph element to hold message */

    var p = document.createElement( 'p' );

    /* Add message text */

    p.appendChild( document.createTextNode( msg ) );

    // Optionally add a link here

    /* Add the whole message to notice div */

    div.appendChild( p );

    /* Create Dismiss icon */

    var b = document.createElement( 'button' );
    b.setAttribute( 'type', 'button' );
    b.classList.add( 'notice-dismiss' );

    /* Add screen reader text to Dismiss icon */

    var bSpan = document.createElement( 'span' );
    bSpan.classList.add( 'screen-reader-text' );
    bSpan.appendChild( document.createTextNode( 'Dismiss this notice' ) );
    b.appendChild( bSpan );

    /* Add Dismiss icon to notice */

    div.appendChild( b );

    /* Insert notice after the first h1 */

    var h1 = document.getElementsByClassName( 'mbp-table-head' )[0];
    h1.parentNode.insertBefore( div, h1);


    /* Make the notice dismissable when the Dismiss icon is clicked */

    b.addEventListener( 'click', function () {
        div.parentNode.removeChild( div );
    });

    jQuery('html, body').animate({
        scrollTop: (jQuery(div).offset().top - 300)
    }, 1000);
}

export default AdminNotice;
