jQuery( function( $ ) {
    ( function() {
        try {

            $( '#ltcr-title:disabled' ).css( 'cursor', 'default' );

            $( 'input#ltcr-title' ).mouseover( function() {
                $( this ).not( '.focus' ).addClass( 'mouseover' );
            } );

            $( 'input#ltcr-title' ).mouseout( function() {
                $( this ).removeClass( 'mouseover' );
            } );

            $( 'input#ltcr-title' ).focus( function() {
                $( this ).addClass( 'focus' ).removeClass( 'mouseover' );
            } );

            $( 'input#ltcr-title' ).blur( function() {
                $( this ).removeClass( 'focus' );
            } );

            $( 'input#ltcr-title' ).change( function() {
                updateTag();
            } );

            updateTag();

        } catch ( e ) {
        }
    }() );

    function updateTag() {
        var title = $( 'input#ltcr-title' ).val();

        if ( title ) {
            title = title.replace(/["'\[\]]/g, '' );
        }

        $( 'input#ltcr-title' ).val( title );
        var postId = $('input#post_id' ).val();
        var tag    = '[ltcr id="' + postId + '" title="' + title + '"]';
        $('input#ltcr-anchor-text' ).val( tag );

        var oldId = $( 'input#ltcr-id' ).val();

        if ( 0 !== parseInt( oldId, 10 ) ) {
            var tagOld = '[ltcr ' + oldId + ' "' + title + '"]';
            $( 'input#ltcr-anchor-text-old' ).val( tagOld ).parent( 'p.tagcode' ).show();
        }
    }

} );