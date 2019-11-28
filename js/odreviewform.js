function odrfControls( id ) {

    var formWrap = document.getElementById( id ),
        ratingCtrls = formWrap.querySelectorAll( '.odrf-field-rating' );

    for( var i = 0; i < ratingCtrls.length; i++ )

        new odrfRatingField( ratingCtrls[i] );

}
function odrfRatingField( node ) {

    var field = this;

    field.stars = node.querySelectorAll( '.odrf-star' );

    function focus( index ) {

        for( var i = 0; i < field.stars.length; i++ ) {

            if( i <= index )

                field.stars[i].classList.add( 'odrf-star-focus' );

            else

                field.stars[i].classList.remove( 'odrf-star-focus' );

        }

    }

    Object.assign( field, {
        starContainer: node.querySelector( '.odrf-stars-wrap' ),
        stars: node.querySelectorAll( '.odrf-star' ),
        focus: focus,
        input: node.querySelector( 'input' )
    } );

    field.stars.forEach( function( btnNode, i ) {
        btnNode.addEventListener( 'mouseenter', function() {
            focus( i );
        } );
        btnNode.addEventListener( 'click', function() {
            var value = (i +1).toString(),
                isValue = field.input.value == value;

            field.input.value = isValue ? '' : value;

            focus( isValue ? -1 : i );
        } );
    } );


    field.starContainer.addEventListener( 'mouseleave', function() {
        focus( field.input.value ? parseInt( field.input.value ) - 1 : -1 );
    } );

}