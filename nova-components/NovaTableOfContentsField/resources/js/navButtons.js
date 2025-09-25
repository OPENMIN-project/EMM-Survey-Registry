export const addPreviousButton = (target, previous) => {
  const html = target.innerHTML
  const prevElement = `
      <svg onclick="document.getElementById('${previous.id}').scrollIntoView();" style="margin-left: 1rem; width: 1.4rem; cursor: pointer"
      xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <title>${previous.innerText}</title>
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
      </svg>
      `
  target.innerHTML = html + prevElement
}
export const addNextButton = (target, next) => {
  const html = target.innerHTML
  const nextElement = `<svg onclick="document.getElementById('${next.id}').scrollIntoView();" style="margin-left: 0.5rem; width: 1.4rem; cursor: pointer"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                             <title>${next.innerText}</title>
                              <path stroke-linecap="round" stroke-linejoin="round" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>`
  target.innerHTML = html + nextElement
}

export default { addPreviousButton, addNextButton }
