//change title
window.onload = ()=>{document.title = document.getElementsByClassName('entry-title')[0].innerHTML.trim();}
function createVCard() {
  const user_name = document.getElementsByClassName('entry-title')[0]!==undefined ?document.getElementsByClassName('entry-title')[0].innerHTML.trim():'';
  const email = document.getElementsByClassName('email')[0]!==undefined?document.getElementsByClassName('email')[0].innerHTML.trim():"";
  const website = document.getElementsByClassName('website')[0]!==undefined?document.getElementsByClassName('website')[0].innerHTML.trim():'';
  const address = document.getElementsByClassName('address')[0]!==undefined?document.getElementsByClassName('address')[0].innerHTML.trim():'';
  const mobile = document.getElementsByClassName('mobile-data')[0]!==undefined?document.getElementsByClassName('mobile-data')[0].innerHTML.trim():'';
  const phone = document.getElementsByClassName('phone-data')[0]!==undefined?document.getElementsByClassName('phone-data')[0].innerHTML.trim():'';
  const fax = document.getElementsByClassName('fax-data')[0]!==undefined?document.getElementsByClassName('fax-data')[0].innerHTML.trim():'';
  const linkedin = document.getElementsByClassName('linkedin')[0]!==undefined?document.getElementsByClassName('linkedin')[0].href.trim():"";
  const designation = document.getElementsByClassName('designation')[0]!==undefined?document.getElementsByClassName('designation')[0].innerHTML.trim():'';
  const img = document.querySelector('.profile-photo img').src!==undefined? document.querySelector('.profile-photo img').src.trim():'';

  const tel = mobile===""?phone:mobile;

  var vcardData = [
    'BEGIN:VCARD',
    'VERSION:3.0',
    'PHOTO;VALUE#URI;TYPE#JPG:'+img,
    'N:' + user_name,
    'TEL;TYPE=work,VOICE:' + tel,
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