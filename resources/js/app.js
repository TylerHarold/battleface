require('./bootstrap');

global.axios = require('axios');
global.$ = global.jQuery = require('jquery');

function registerLoginButton() {
    $("#login").on("click", function() {
        let data = {
            email: $("#email").val(),
            password: $("#password").val()
        };

        axios.post('/api/auth/login', data)
            .then(function(res) {
                console.log(res);

                /*
                    Was short on time & new to using JWT. Couldn't find a concise answer on how to securely store tokens
                    for this particular situation.
                 */
            })
            .catch(function(err) {
                alert(err);
            });
    })
}

function registerRegisterButton() {
    $("#register").on("click", function() {
        let data = {
            name: $("#name").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            password_confirmation: $("#password_confirmation").val()
        }

        axios.post('/api/auth/register', data)
            .then(function(res) {

            })
            .catch(function(err) {
               alert(err);
            });
    })
}

function registerQuoteButton() {
    $("#quote").on("click", function() {
        let data = {
            age: $("#age").val(),
            currency_id: $("#currency_id").val(),
            start_date: $("#start_date").val(),
            end_date: $("#end_date").val()
        }
    })
}

$(document).ready(function() {
    registerLoginButton();
    registerRegisterButton();
    registerQuoteButton();
});





