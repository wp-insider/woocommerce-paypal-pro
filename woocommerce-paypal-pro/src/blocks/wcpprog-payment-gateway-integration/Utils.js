const { getSetting } = window.wc.wcSettings

export function getSettings(key, defaultValue = null){
    const settings = getSetting( 'paypalpro_data', {} );
    return settings[key] || defaultValue;
}
