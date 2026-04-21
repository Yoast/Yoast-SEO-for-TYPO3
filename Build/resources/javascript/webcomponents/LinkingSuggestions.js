import React from 'react';
import LoadingSpinner from './LoadingSpinner';

const LinkingSuggestions = ({links, isChecking}) => {
    if (isChecking) {
        return <LoadingSpinner/>
    }

    if (links && links.length > 0) {
        return (
            <table className="table" style={{width: 'auto'}}>
                <thead>
                <tr>
                    <th>Label</th>
                    <th>Record</th>
                    <th>Linked</th>
                </tr>
                {links.map((link) => (
                    <tr key={link.recordType + link.id}>
                        <td>
                            {link.label}{' '}{parseInt(link.cornerstone) === 1 && '*'}
                        </td>
                        <td>
                            {link.recordType} [uid={link.id}]
                        </td>
                        <td className="text-center">
                            {link.active
                                ? <button className="btn btn-success btn-sm">&nbsp;✓&nbsp;</button>
                                : <button className="btn btn-danger btn-sm">&nbsp;✗&nbsp;</button>
                            }
                        </td>
                    </tr>
                ))}
                </thead>
            </table>
        );
    }
    return <div className="alert alert-info">
        No results found, make sure you have more than 100 words to analyze.<br/>
        If there's enough content to analyze, it may be that no other content relates to the current content.
    </div>
}

export default LinkingSuggestions;