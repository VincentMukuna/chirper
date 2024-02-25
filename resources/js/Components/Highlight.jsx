export default function Highlight({text, highlight, className, ...props}){
    const parts = text.split(new RegExp(`(${highlight})`, 'gi'));
    return(
        <span className={className} {...props}>
            {parts.map((part, i) =>
                part.toLowerCase() === highlight.toLowerCase()
                    ? <mark key={i} className="bg-yellow-200">{part}</mark>
                    : part
            )}
        </span>
    )
}
