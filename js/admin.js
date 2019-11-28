(function($, options) {

    if( options.locations ) {

        options.locations = new LocationLinks( options.locations );

        if( options.locations.unregistered.length )

            registerLinkLocations( options.locations.unregistered );

    }

    function LocationLinks( locations ) {

        var _this = this;

        function keys() {
            return Object.keys( _this.locations );
        }

        Object.assign( this, {
            keys: keys,
            locations: Object.assign({}, locations ),
            unregistered: []
        } );

        var keys = _this.keys();

        for( var i = 0; i < keys.length; i++ ) {

            _this.locations[ keys[i] ] = new LocationLink( keys[i], _this.locations[ keys[i] ] );

            if( ! _this.locations[ keys[i] ].linkId )

                _this.unregistered.push( keys[i] );

        }

    }

    function LocationLink( id, details ) {

        var _this = this;

        function update( details ) {
            Object.assign( _this, details );
        }

        Object.assign( _this, {
            id: id,
            update: update,
            status: $( '.location-status-text', '#location-'+id ),
            statusSpinner: $( '.spinner', '#location-'+id )
        }, details );

    }

    function registerLinkLocations( locationIds ) {

        var locationId = locationIds.pop();

        options.locations.locations[ locationId ].status.text( 'Registering...' );
        options.locations.locations[ locationId ].statusSpinner.addClass( 'is-active' );

        $.post( {
            url: options.apiUrl +'links/'+ locationId,
            data: [],
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', options.nonce );
            }
        }).success( function( response ) {

            options.locations.locations[ locationId ].status.text( 'Registered' );
            options.locations.locations[ locationId ].statusSpinner.removeClass( 'is-active' );

            options.locations.locations[ locationId ].update( response.responseJSON );

            if( locationIds.length )

                registerLinkLocations( locationIds );

        }).fail( function( response ) {

            var message = ( response.responseJSON && response.responseJSON.message )

                ?  response.responseJSON.message

                : 'Error';

            options.locations.locations[ locationId ].status.text( message );

            options.locations.locations[ locationId ].statusSpinner.removeClass( 'is-active' );

        } );

    }

})( jQuery, odrfAdminOptions );