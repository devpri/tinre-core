import Noty from 'noty'

if (window.messages != undefined) {
  window.messages.forEach(function (message) {
    new Noty({
      type: message.type,
      text: message.text,
    }).show()
  })
}
