<style>
    .rf-auth,
    .rf-auth * { box-sizing: border-box; }

    .rf-auth {
        display: grid;
        min-height: 660px;
        place-items: center;
        padding: 56px 24px;
        background:
            radial-gradient(ellipse at 8% 12%, rgba(255, 255, 255, .95), transparent 34%),
            linear-gradient(135deg, #f4f4f4 0%, #e8e8e8 50%, #f8f8f8 100%);
    }

    .rf-auth__card {
        display: grid;
        grid-template-columns: minmax(280px, .82fr) minmax(390px, 1.18fr);
        width: min(100%, 1000px);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .8);
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 28px 80px rgba(0, 0, 0, .14);
    }

    .rf-auth__card--login { max-width: 900px; }
    .rf-auth__intro {
        position: relative;
        display: flex;
        min-height: 560px;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        overflow: hidden;
        padding: 54px 44px;
        color: #fff;
        background: linear-gradient(145deg, #090909 0%, #1b1b1b 48%, #383838 100%);
    }

    .rf-auth__intro::before,
    .rf-auth__intro::after {
        position: absolute;
        z-index: 0;
        border: 1px solid rgba(255, 255, 255, .09);
        border-radius: 50%;
        content: "";
        pointer-events: none;
    }

    .rf-auth__intro::before { top: -140px; right: -150px; width: 380px; height: 380px; }
    .rf-auth__intro::after { right: -80px; bottom: -210px; width: 390px; height: 390px; }
    .rf-auth__intro > * { position: relative; z-index: 1; }

    .rf-auth__logo {
        display: inline-flex;
        min-width: 144px;
        min-height: 72px;
        align-items: center;
        justify-content: center;
        margin-bottom: 50px;
        padding: 8px 14px;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(255,255,255,.16), rgba(255,255,255,.04));
    }

    .rf-auth__logo img { display: block; width: auto; max-width: 160px; max-height: 56px; object-fit: contain; }
    .rf-auth__eyebrow { display: block; margin-bottom: 12px; font-size: 10px; font-weight: 700; letter-spacing: .2em; }
    .rf-auth__intro .rf-auth__eyebrow { color: #c9c9c9; }
    .rf-auth__intro h1 { max-width: 340px; margin: 0 0 16px; color: #fff; font-size: clamp(32px, 4vw, 44px); font-weight: 500; letter-spacing: -.04em; line-height: 1.08; }
    .rf-auth__intro > p { max-width: 330px; margin: 0; color: #d0d0d0; font-size: 15px; line-height: 1.75; }
    .rf-auth__switch { display: grid; gap: 10px; margin-top: 48px; color: #c7c7c7; font-size: 13px; }
    .rf-auth__switch a { width: fit-content; color: #fff; font-weight: 600; text-decoration: none; }
    .rf-auth__switch a span { margin-left: 7px; transition: margin .2s ease; }
    .rf-auth__switch a:hover span { margin-left: 12px; }

    .rf-auth__form-panel { align-self: center; padding: 58px clamp(30px, 6vw, 72px); }
    .rf-auth__form-heading { margin-bottom: 32px; }
    .rf-auth__form-heading .rf-auth__eyebrow { color: #777; }
    .rf-auth__form-heading h2 { margin: 0 0 8px; color: #111; font-size: clamp(28px, 3vw, 34px); font-weight: 500; letter-spacing: -.04em; }
    .rf-auth__form-heading p { margin: 0; color: #777; font-size: 14px; line-height: 1.6; }
    .rf-auth__form { display: grid; gap: 20px; }
    .rf-auth__field { min-width: 0; }
    .rf-auth__field label { display: block; margin-bottom: 8px; color: #292929; font-size: 13px; font-weight: 600; }
    .rf-auth__field input {
        display: block;
        width: 100%;
        height: 50px;
        padding: 0 15px;
        border: 1px solid #d7d7d7;
        border-radius: 9px;
        outline: 0;
        background: linear-gradient(180deg, #fff, #fafafa);
        color: #171717;
        font: inherit;
        font-size: 14px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .rf-auth__field input::placeholder { color: #aaa; }
    .rf-auth__field input:focus { border-color: #333; box-shadow: 0 0 0 3px rgba(0, 0, 0, .08); }
    .rf-auth__hint { display: block; margin-top: 7px; color: #858585; font-size: 11px; }
    .rf-auth__error { display: block; margin-top: 7px; color: #b42318; font-size: 12px; line-height: 1.5; }
    .rf-auth__notice { margin: -12px 0 22px; padding: 12px 14px; border: 1px solid #d5d5d5; border-radius: 8px; background: #f7f7f7; color: #333; font-size: 13px; }
    .rf-auth__form-options { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-top: -3px; color: #555; font-size: 12px; }
    .rf-auth__remember { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .rf-auth__remember input,
    .rf-auth__terms input { width: 16px; height: 16px; margin: 0; accent-color: #111; }
    .rf-auth__form-options a { color: #222; text-decoration: underline; text-decoration-color: #bbb; text-underline-offset: 3px; }
    .rf-auth__submit {
        display: flex;
        min-height: 52px;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        margin-top: 4px;
        border: 1px solid #111;
        border-radius: 9px;
        background: linear-gradient(110deg, #090909, #333 52%, #111);
        color: #fff;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .rf-auth__submit:hover { transform: translateY(-1px); box-shadow: 0 9px 22px rgba(0, 0, 0, .2); }
    .rf-auth__mobile-switch { display: none; }
    .rf-auth__form--register { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 14px; }
    .rf-auth__terms { display: flex; grid-column: 1 / -1; align-items: flex-start; gap: 9px; color: #666; font-size: 12px; line-height: 1.5; cursor: pointer; }
    .rf-auth__terms input { flex: 0 0 auto; margin-top: 1px; }
    .rf-auth__form--register .rf-auth__submit { grid-column: 1 / -1; }
    .rf-auth__card--register .rf-auth__intro { min-height: 700px; }
    .rf-auth__card--register .rf-auth__form-panel { padding-top: 44px; padding-bottom: 44px; }

    @media (max-width: 760px) {
        .rf-auth { min-height: auto; padding: 30px 16px; }
        .rf-auth__card { grid-template-columns: 1fr; width: min(100%, 520px); border-radius: 18px; }
        .rf-auth__intro,
        .rf-auth__card--register .rf-auth__intro { min-height: auto; padding: 28px 26px 30px; }
        .rf-auth__logo { min-width: 112px; min-height: 58px; margin-bottom: 25px; }
        .rf-auth__logo img { max-width: 128px; max-height: 44px; }
        .rf-auth__intro h1 { font-size: 32px; }
        .rf-auth__intro > p { font-size: 13px; }
        .rf-auth__switch { display: none; }
        .rf-auth__form-panel,
        .rf-auth__card--register .rf-auth__form-panel { padding: 32px 26px; }
        .rf-auth__form-heading { margin-bottom: 24px; }
        .rf-auth__mobile-switch { display: block; margin: 22px 0 0; color: #666; font-size: 13px; text-align: center; }
        .rf-auth__mobile-switch a { color: #111; font-weight: 600; }
    }

    @media (max-width: 480px) {
        .rf-auth__form--register { grid-template-columns: 1fr; }
        .rf-auth__terms,
        .rf-auth__form--register .rf-auth__submit { grid-column: auto; }
        .rf-auth__form-options { align-items: flex-start; }
    }
</style>
