function createVCard() {
  const user_name = document.getElementsByClassName('entry-title')[0].innerHTML.trim();
  const email = document.getElementsByClassName('email')[0].innerHTML.trim();
  const website = document.getElementsByClassName('website')[0].innerHTML.trim();
  const address = document.getElementsByClassName('address')[0].innerHTML.trim();
  const phone = document.getElementsByClassName('phone-data')[0].innerHTML.trim();
  const linkedin = document.getElementsByClassName('linkedin')[0].href.trim();
  const designation = document.getElementsByClassName('designation')[0].innerHTML.trim();

  var vcardData = [
    'BEGIN:VCARD',
    'VERSION:3.0',
    'N:' + user_name,
    'TEL;TYPE=work,VOICE:' + phone,
    'EMAIL:' + email,
    'ORG:' + 'Sterling Administration',
    'TITLE:' + designation,
    'ADR;TYPE=WORK,PREF:' + address,
    'URL:' + website,
    'X-SOCIALPROFILE;type=linkedin:' + linkedin,
    'END:VCARD'
  ];

  var vcardContent = vcardData.join('\n');
  var vcardBlob = new Blob([vcardContent], { type: 'text/vcard;charset=utf-8' });

  if (navigator.msSaveBlob) {
    // For Microsoft Edge and IE
    navigator.msSaveBlob(vcardBlob, user_name + '.vcf');
  } else {
    // For other browsers
    downloadLink = document.getElementById('download-vcf');
    downloadLink.href = URL.createObjectURL(vcardBlob);
    downloadLink.download = user_name + '.vcf';
    downloadLink.click();
  }
}