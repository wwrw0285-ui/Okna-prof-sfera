function openModal(){const m=document.getElementById('modal');if(m){m.style.display='flex';document.body.style.overflow='hidden';}}
function closeModal(){const m=document.getElementById('modal');if(m){m.style.display='none';document.body.style.overflow='';}}
window.addEventListener('click',e=>{const m=document.getElementById('modal');if(m&&e.target===m)closeModal();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});
document.addEventListener('DOMContentLoaded',()=>{
    const burger=document.querySelector('.burger');
    const nav=document.querySelector('nav');
    if(burger&&nav){burger.addEventListener('click',()=>nav.classList.toggle('open'));}
    document.querySelectorAll('nav li.has-dropdown > a').forEach(link=>{
        link.addEventListener('click',e=>{
            if(window.innerWidth<=900){e.preventDefault();link.parentElement.classList.toggle('open');}
        });
    });
});
async function submitForm(event){
    event.preventDefault();
    const form=event.target;
    const nameInput=form.querySelector('input[name="name"]');
    const phoneInput=form.querySelector('input[name="phone"]');
    const msgInput=form.querySelector('textarea[name="message"]');
    const submitBtn=form.querySelector('button[type="submit"]');
    const name=nameInput.value.trim();
    const phone=phoneInput.value.trim();
    const message=msgInput?msgInput.value.trim():'';
    if(!name){alert('Пожалуйста, укажите ваше имя');nameInput.focus();return;}
    if(!phone||phone.replace(/\D/g,'').length<10){alert('Пожалуйста, укажите корректный номер телефона');phoneInput.focus();return;}
    const originalText=submitBtn.textContent;
    submitBtn.disabled=true;submitBtn.textContent='Отправка...';
    try{
        const response=await fetch('send.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:name,phone:phone,message:message,source:document.title})});
        const result=await response.json();
        if(result.ok){alert('Спасибо, '+name+'! Заявка отправлена. Мы перезвоним в течение 5 минут.');form.reset();closeModal();}
        else{alert('Ошибка: '+(result.error||'попробуйте позвонить нам напрямую'));}
    }catch(err){console.error(err);alert('Не удалось отправить заявку. Позвоните нам: +7 (981) 024-21-77');}
    finally{submitBtn.disabled=false;submitBtn.textContent=originalText;}
}
