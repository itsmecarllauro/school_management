function sendMail() {
  let parms = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    verification_code: document.getElementById("verification_code").value,
  };

  emailjs
    .send("service_bafplgr", "template_jdwe1ur", parms)
    .then(alert("Email sent successfully!"));
}
