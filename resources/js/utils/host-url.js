export function getHostUrl () {
    const protocol = location.protocol
    const slashes = protocol.concat('//')
    return slashes.concat(window.location.hostname)
}
